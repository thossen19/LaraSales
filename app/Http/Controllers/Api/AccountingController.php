<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JournalEntryRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\JournalEntryResource;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    // Accounts
    public function accounts(Request $request)
    {
        $query = Account::where('company_id', $request->user()->company_id)
            ->with(['parent', 'children']);

        if ($request->has('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('code')->get();

        return AccountResource::collection($accounts);
    }

    public function accountTree(Request $request)
    {
        $accounts = Account::where('company_id', $request->user()->company_id)
            ->whereNull('parent_code')
            ->with(['children' => function ($query) {
                $query->with(['children'])->orderBy('code');
            }])
            ->orderBy('code')
            ->get();

        return AccountResource::collection($accounts);
    }

    // Journal Entries
    public function journalEntries(Request $request)
    {
        $query = JournalEntry::with(['lines.account', 'createdBy', 'postedBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('is_posted', $request->status === 'posted');
        }

        if ($request->has('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        if ($request->has('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        $entries = $query->orderBy('entry_date', 'desc')->paginate(20);

        return JournalEntryResource::collection($entries);
    }

    public function storeJournalEntry(JournalEntryRequest $request)
    {
        try {
            DB::beginTransaction();

            $journalEntry = JournalEntry::create([
                'company_id' => $request->user()->company_id,
                'entry_date' => $request->entry_date,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id,
                'description' => $request->description,
                'total_debit' => 0,
                'total_credit' => 0,
                'is_posted' => false,
                'created_by' => $request->user()->id,
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($request->lines as $lineData) {
                $account = Account::findOrFail($lineData['account_id']);
                
                $journalEntryLine = JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $lineData['account_id'],
                    'description' => $lineData['description'],
                    'debit_amount' => $lineData['debit_amount'] ?? 0,
                    'credit_amount' => $lineData['credit_amount'] ?? 0,
                ]);

                $totalDebit += $journalEntryLine->debit_amount;
                $totalCredit += $journalEntryLine->credit_amount;
            }

            $journalEntry->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            if (abs($totalDebit - $totalCredit) > 0.01) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Journal entry must be balanced (debits must equal credits)',
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'difference' => abs($totalDebit - $totalCredit)
                ], Response::HTTP_BAD_REQUEST);
            }

            DB::commit();

            return new JournalEntryResource($journalEntry->load(['lines.account', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create journal entry: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showJournalEntry(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new JournalEntryResource($journalEntry->load(['lines.account', 'createdBy', 'postedBy']));
    }

    public function postJournalEntry(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($journalEntry->is_posted) {
            return response()->json(['message' => 'Journal entry is already posted'], Response::HTTP_BAD_REQUEST);
        }

        if (!$journalEntry->isBalanced()) {
            return response()->json(['message' => 'Cannot post unbalanced journal entry'], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $journalEntry->update([
                'is_posted' => true,
                'posted_at' => now(),
                'posted_by' => $request->user()->id,
            ]);

            // Update account balances
            foreach ($journalEntry->lines as $line) {
                $account = $line->account;
                $newBalance = $account->current_balance;

                if ($account->account_type === 'asset' || $account->account_type === 'expense') {
                    if ($line->debit_amount > 0) {
                        $newBalance += $line->debit_amount;
                    }
                    if ($line->credit_amount > 0) {
                        $newBalance -= $line->credit_amount;
                    }
                } else { // liability, equity, revenue
                    if ($line->debit_amount > 0) {
                        $newBalance -= $line->debit_amount;
                    }
                    if ($line->credit_amount > 0) {
                        $newBalance += $line->credit_amount;
                    }
                }

                $account->update(['current_balance' => $newBalance]);
            }

            DB::commit();

            return new JournalEntryResource($journalEntry->load(['lines.account', 'createdBy', 'postedBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to post journal entry: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trialBalance(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $accounts = Account::where('company_id', $request->user()->company_id)
            ->where('is_active', true)
            ->where('level', '>', 1) // Exclude parent accounts
            ->orderBy('code')
            ->get();

        $trialBalance = $accounts->map(function ($account) use ($date) {
            $debitTotal = $account->journalEntryLines()
                ->whereHas('journalEntry', function ($query) use ($date) {
                    $query->where('entry_date', '<=', $date)->where('is_posted', true);
                })
                ->sum('debit_amount');

            $creditTotal = $account->journalEntryLines()
                ->whereHas('journalEntry', function ($query) use ($date) {
                    $query->where('entry_date', '<=', $date)->where('is_posted', true);
                })
                ->sum('credit_amount');

            $balance = $account->current_balance;

            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'account_type' => $account->account_type,
                'debit_total' => $debitTotal,
                'credit_total' => $creditTotal,
                'balance' => $balance,
                'debit_balance' => $account->account_type === 'asset' || $account->account_type === 'expense' ? max(0, $balance) : 0,
                'credit_balance' => $account->account_type === 'liability' || $account->account_type === 'equity' || $account->account_type === 'revenue' ? max(0, $balance) : 0,
            ];
        });

        $totalDebits = $trialBalance->sum('debit_balance');
        $totalCredits = $trialBalance->sum('credit_balance');

        return response()->json([
            'date' => $date,
            'accounts' => $trialBalance,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'is_balanced' => abs($totalDebits - $totalCredits) < 0.01,
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $accounts = Account::where('company_id', $request->user()->company_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $assets = $accounts->where('account_type', 'asset');
        $liabilities = $accounts->where('account_type', 'liability');
        $equity = $accounts->where('account_type', 'equity');

        $totalAssets = $assets->sum('current_balance');
        $totalLiabilities = $liabilities->sum('current_balance');
        $totalEquity = $equity->sum('current_balance');

        return response()->json([
            'date' => $date,
            'assets' => [
                'total' => $totalAssets,
                'accounts' => $assets->map(function ($account) {
                    return [
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance' => $account->current_balance,
                    ];
                }),
            ],
            'liabilities' => [
                'total' => $totalLiabilities,
                'accounts' => $liabilities->map(function ($account) {
                    return [
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance' => $account->current_balance,
                    ];
                }),
            ],
            'equity' => [
                'total' => $totalEquity,
                'accounts' => $equity->map(function ($account) {
                    return [
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance' => $account->current_balance,
                    ];
                }),
            ],
            'total_liabilities_equity' => $totalLiabilities + $totalEquity,
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ]);
    }

    public function profitLoss(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $accounts = Account::where('company_id', $request->user()->company_id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $revenues = $accounts->where('account_type', 'revenue');
        $expenses = $accounts->where('account_type', 'expense');

        $totalRevenue = 0;
        $totalExpenses = 0;

        // Calculate period totals
        foreach ($revenues as $account) {
            $periodTotal = $account->journalEntryLines()
                ->whereHas('journalEntry', function ($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('entry_date', [$dateFrom, $dateTo])
                          ->where('is_posted', true);
                })
                ->sum('credit_amount');
            $totalRevenue += $periodTotal;
        }

        foreach ($expenses as $account) {
            $periodTotal = $account->journalEntryLines()
                ->whereHas('journalEntry', function ($query) use ($dateFrom, $dateTo) {
                    $query->whereBetween('entry_date', [$dateFrom, $dateTo])
                          ->where('is_posted', true);
                })
                ->sum('debit_amount');
            $totalExpenses += $periodTotal;
        }

        $grossProfit = $totalRevenue;
        $netIncome = $totalRevenue - $totalExpenses;

        return response()->json([
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'accounts' => $revenues->map(function ($account) use ($dateFrom, $dateTo) {
                    $periodTotal = $account->journalEntryLines()
                        ->whereHas('journalEntry', function ($query) use ($dateFrom, $dateTo) {
                            $query->whereBetween('entry_date', [$dateFrom, $dateTo])
                                  ->where('is_posted', true);
                        })
                        ->sum('credit_amount');

                    return [
                        'code' => $account->code,
                        'name' => $account->name,
                        'period_total' => $periodTotal,
                    ];
                }),
            ],
            'expenses' => [
                'total' => $totalExpenses,
                'accounts' => $expenses->map(function ($account) use ($dateFrom, $dateTo) {
                    $periodTotal = $account->journalEntryLines()
                        ->whereHas('journalEntry', function ($query) use ($dateFrom, $dateTo) {
                            $query->whereBetween('entry_date', [$dateFrom, $dateTo])
                                  ->where('is_posted', true);
                        })
                        ->sum('debit_amount');

                    return [
                        'code' => $account->code,
                        'name' => $account->name,
                        'period_total' => $periodTotal,
                    ];
                }),
            ],
            'gross_profit' => $grossProfit,
            'net_income' => $netIncome,
        ]);
    }
}
