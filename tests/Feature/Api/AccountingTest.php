<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_accounts()
    {
        Account::factory()->count(5)->create(['company_id' => $this->user->company_id]);

        $response = $this->getJson('/api/accounting/accounts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'code',
                    'name',
                    'account_type',
                    'current_balance',
                    'is_active',
                ]
            ]);
    }

    public function test_can_get_account_tree()
    {
        // Create parent and child accounts
        $parentAccount = Account::factory()->create([
            'company_id' => $this->user->company_id,
            'parent_code' => null,
            'level' => 1,
        ]);

        Account::factory()->count(3)->create([
            'company_id' => $this->user->company_id,
            'parent_code' => $parentAccount->code,
            'level' => 2,
        ]);

        $response = $this->getJson('/api/accounting/accounts/tree');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'code',
                    'name',
                    'children' => [
                        '*' => [
                            'id',
                            'code',
                            'name',
                        ]
                    ],
                ]
            ]);
    }

    public function test_can_create_journal_entry()
    {
        $debitAccount = Account::factory()->create([
            'company_id' => $this->user->company_id,
            'account_type' => 'asset',
        ]);

        $creditAccount = Account::factory()->create([
            'company_id' => $this->user->company_id,
            'account_type' => 'liability',
        ]);

        $data = [
            'entry_date' => now()->toDateString(),
            'description' => 'Test journal entry',
            'lines' => [
                [
                    'account_id' => $debitAccount->id,
                    'description' => 'Debit line',
                    'debit_amount' => 1000,
                ],
                [
                    'account_id' => $creditAccount->id,
                    'description' => 'Credit line',
                    'credit_amount' => 1000,
                ],
            ],
        ];

        $response = $this->postJson('/api/accounting/journal-entries', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'entry_number',
                'entry_date',
                'description',
                'total_debit',
                'total_credit',
                'is_posted',
                'lines',
            ]);

        $this->assertDatabaseHas('journal_entries', [
            'company_id' => $this->user->company_id,
            'description' => 'Test journal entry',
            'total_debit' => 1000,
            'total_credit' => 1000,
        ]);

        $this->assertDatabaseHas('journal_entry_lines', [
            'account_id' => $debitAccount->id,
            'debit_amount' => 1000,
            'credit_amount' => 0,
        ]);

        $this->assertDatabaseHas('journal_entry_lines', [
            'account_id' => $creditAccount->id,
            'debit_amount' => 0,
            'credit_amount' => 1000,
        ]);
    }

    public function test_cannot_create_unbalanced_journal_entry()
    {
        $debitAccount = Account::factory()->create([
            'company_id' => $this->user->company_id,
            'account_type' => 'asset',
        ]);

        $creditAccount = Account::factory()->create([
            'company_id' => $this->user->company_id,
            'account_type' => 'liability',
        ]);

        $data = [
            'entry_date' => now()->toDateString(),
            'description' => 'Unbalanced journal entry',
            'lines' => [
                [
                    'account_id' => $debitAccount->id,
                    'debit_amount' => 1000,
                ],
                [
                    'account_id' => $creditAccount->id,
                    'credit_amount' => 900,
                ],
            ],
        ];

        $response = $this->postJson('/api/accounting/journal-entries', $data);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Journal entry must be balanced (debits must equal credits)',
            ]);
    }

    public function test_can_post_journal_entry()
    {
        $journalEntry = JournalEntry::factory()->create([
            'company_id' => $this->user->company_id,
            'is_posted' => false,
            'total_debit' => 1000,
            'total_credit' => 1000,
        ]);

        $response = $this->postJson("/api/accounting/journal-entries/{$journalEntry->id}/post");

        $response->assertStatus(200);

        $this->assertDatabaseHas('journal_entries', [
            'id' => $journalEntry->id,
            'is_posted' => true,
            'posted_by' => $this->user->id,
        ]);
    }

    public function test_cannot_post_already_posted_journal_entry()
    {
        $journalEntry = JournalEntry::factory()->create([
            'company_id' => $this->user->company_id,
            'is_posted' => true,
        ]);

        $response = $this->postJson("/api/accounting/journal-entries/{$journalEntry->id}/post");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Journal entry is already posted',
            ]);
    }

    public function test_can_get_trial_balance()
    {
        $response = $this->getJson('/api/accounting/trial-balance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'date',
                'accounts',
                'total_debits',
                'total_credits',
                'is_balanced',
            ]);
    }

    public function test_can_get_balance_sheet()
    {
        $response = $this->getJson('/api/accounting/balance-sheet');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'date',
                'assets' => [
                    'total',
                    'accounts',
                ],
                'liabilities' => [
                    'total',
                    'accounts',
                ],
                'equity' => [
                    'total',
                    'accounts',
                ],
                'total_liabilities_equity',
                'is_balanced',
            ]);
    }

    public function test_can_get_profit_loss()
    {
        $response = $this->getJson('/api/accounting/profit-loss');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period' => [
                    'from',
                    'to',
                ],
                'revenue' => [
                    'total',
                    'accounts',
                ],
                'expenses' => [
                    'total',
                    'accounts',
                ],
                'gross_profit',
                'net_income',
            ]);
    }

    public function test_cannot_access_other_company_journal_entry()
    {
        $otherUser = User::factory()->create();
        $journalEntry = JournalEntry::factory()->create([
            'company_id' => $otherUser->company_id,
        ]);

        $response = $this->getJson("/api/accounting/journal-entries/{$journalEntry->id}");

        $response->assertStatus(403);
    }

    public function test_journal_entry_validation()
    {
        $response = $this->postJson('/api/accounting/journal-entries', [
            'entry_date' => null,
            'description' => '',
            'lines' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['entry_date', 'description', 'lines']);
    }
}
