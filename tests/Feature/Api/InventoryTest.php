<?php

namespace Tests\Feature\Api;

use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;
    protected $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->item = Item::factory()->create([
            'company_id' => $this->user->company_id,
            'is_stock_item' => true,
        ]);

        $this->warehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);
    }

    public function test_can_get_current_stock()
    {
        // Create initial inventory transaction
        InventoryTransaction::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'in',
            'quantity' => 100,
            'quantity_before' => 0,
            'quantity_after' => 100,
        ]);

        $response = $this->getJson('/api/inventory/current');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'code',
                    'name',
                    'current_stock',
                    'status',
                    'unit_of_measure',
                ]
            ]);
    }

    public function test_can_get_inventory_transactions()
    {
        InventoryTransaction::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'in',
        ]);

        $response = $this->getJson('/api/inventory/transactions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'transaction_type',
                        'quantity',
                        'quantity_before',
                        'quantity_after',
                        'item',
                        'warehouse',
                    ]
                ]
            ]);
    }

    public function test_can_adjust_inventory()
    {
        $data = [
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'warehouse_id' => $this->warehouse->id,
                    'new_quantity' => 50,
                    'unit_cost' => 10,
                    'notes' => 'Manual adjustment',
                ]
            ],
        ];

        $response = $this->postJson('/api/inventory/adjust', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'adjustments' => [
                    '*' => [
                        'item_id',
                        'item_name',
                        'quantity_before',
                        'quantity_after',
                        'adjustment',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'adjustment',
        ]);
    }

    public function test_can_transfer_inventory()
    {
        $sourceWarehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $targetWarehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        // Set up initial stock
        InventoryTransaction::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $sourceWarehouse->id,
            'transaction_type' => 'in',
            'quantity' => 100,
            'quantity_before' => 0,
            'quantity_after' => 100,
        ]);

        $data = [
            'item_id' => $this->item->id,
            'from_warehouse_id' => $sourceWarehouse->id,
            'to_warehouse_id' => $targetWarehouse->id,
            'quantity' => 25,
            'notes' => 'Transfer between warehouses',
        ];

        $response = $this->postJson('/api/inventory/transfer', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'item',
                'from_warehouse',
                'to_warehouse',
                'quantity',
            ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $sourceWarehouse->id,
            'transaction_type' => 'out',
            'reference_type' => 'transfer',
        ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $targetWarehouse->id,
            'transaction_type' => 'in',
            'reference_type' => 'transfer',
        ]);
    }

    public function test_transfer_validation_insufficient_stock()
    {
        $sourceWarehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $targetWarehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $data = [
            'item_id' => $this->item->id,
            'from_warehouse_id' => $sourceWarehouse->id,
            'to_warehouse_id' => $targetWarehouse->id,
            'quantity' => 100, // More than available
        ];

        $response = $this->postJson('/api/inventory/transfer', $data);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Insufficient stock in source warehouse',
            ]);
    }

    public function test_inventory_adjustment_validation()
    {
        $response = $this->postJson('/api/inventory/adjust', [
            'items' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }
}
