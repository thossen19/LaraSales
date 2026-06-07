<?php

namespace Tests\Feature\Api;

use App\Models\BillOfMaterials;
use App\Models\BomItem;
use App\Models\Item;
use App\Models\ProductionOrder;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ManufacturingTest extends TestCase
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
        ]);

        $this->warehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);
    }

    public function test_can_create_bom()
    {
        $componentItem = Item::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $data = [
            'item_id' => $this->item->id,
            'version' => '1.0',
            'status' => 'active',
            'standard_cost' => 100.00,
            'items' => [
                [
                    'component_item_id' => $componentItem->id,
                    'quantity' => 2,
                    'unit_of_measure' => 'pcs',
                    'scrap_percentage' => 5,
                ],
            ],
        ];

        $response = $this->postJson('/api/manufacturing/bom', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'bom_number',
                'version',
                'status',
                'standard_cost',
                'item',
                'bom_items',
            ]);

        $this->assertDatabaseHas('bills_of_materials', [
            'item_id' => $this->item->id,
            'version' => '1.0',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('bom_items', [
            'component_item_id' => $componentItem->id,
            'quantity' => 2,
            'effective_quantity' => 2.1, // 2 * (1 + 0.05)
        ]);
    }

    public function test_can_list_boms()
    {
        BillOfMaterials::factory()->count(3)->create([
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->getJson('/api/manufacturing/bom');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'bom_number',
                    'version',
                    'status',
                    'item',
                ]
            ]);
    }

    public function test_can_create_production_order()
    {
        $data = [
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'order_date' => now()->toDateString(),
            'start_date' => now()->addDay()->toDateString(),
            'finish_date' => now()->addWeek()->toDateString(),
            'quantity_planned' => 100,
            'standard_cost' => 50.00,
        ];

        $response = $this->postJson('/api/manufacturing/production-orders', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'order_number',
                'order_date',
                'status',
                'quantity_planned',
                'item',
                'warehouse',
            ]);

        $this->assertDatabaseHas('production_orders', [
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'planned',
            'quantity_planned' => 100,
        ]);
    }

    public function test_can_release_production_order()
    {
        $productionOrder = ProductionOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => 'planned',
        ]);

        $response = $this->postJson("/api/manufacturing/production-orders/{$productionOrder->id}/release");

        $response->assertStatus(200);

        $this->assertDatabaseHas('production_orders', [
            'id' => $productionOrder->id,
            'status' => 'released',
        ]);
    }

    public function test_can_start_production_order()
    {
        // Create BOM first
        $componentItem = Item::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $bom = BillOfMaterials::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
        ]);

        BomItem::factory()->create([
            'bill_of_materials_id' => $bom->id,
            'component_item_id' => $componentItem->id,
            'quantity' => 1,
            'effective_quantity' => 1,
        ]);

        $productionOrder = ProductionOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'released',
            'quantity_planned' => 10,
        ]);

        // Add inventory for component
        $this->createInventoryTransaction($componentItem, $this->warehouse, 20);

        $response = $this->postJson("/api/manufacturing/production-orders/{$productionOrder->id}/start");

        $response->assertStatus(200);

        $this->assertDatabaseHas('production_orders', [
            'id' => $productionOrder->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('inventory_transactions', [
            'item_id' => $componentItem->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'out',
            'reference_type' => 'production',
            'reference_id' => $productionOrder->id,
            'quantity' => 10, // 10 * 1 effective quantity
        ]);
    }

    public function test_can_complete_production_order()
    {
        $productionOrder = ProductionOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'in_progress',
            'quantity_planned' => 100,
            'quantity_produced' => 50,
        ]);

        $data = [
            'quantity_produced' => 30,
            'quantity_scrapped' => 5,
        ];

        $response = $this->postJson("/api/manufacturing/production-orders/{$productionOrder->id}/complete", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('production_orders', [
            'id' => $productionOrder->id,
            'quantity_produced' => 80, // 50 + 30
            'quantity_scrapped' => 5,
        ]);
    }

    public function test_cannot_start_production_without_materials()
    {
        // Create BOM without inventory
        $componentItem = Item::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $bom = BillOfMaterials::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
        ]);

        BomItem::factory()->create([
            'bill_of_materials_id' => $bom->id,
            'component_item_id' => $componentItem->id,
            'quantity' => 5,
            'effective_quantity' => 5,
        ]);

        $productionOrder = ProductionOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'released',
            'quantity_planned' => 10,
        ]);

        $response = $this->postJson("/api/manufacturing/production-orders/{$productionOrder->id}/start");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Insufficient materials',
            ]);
    }

    public function test_can_get_work_centers()
    {
        $response = $this->getJson('/api/manufacturing/work-centers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'code',
                    'name',
                    'hourly_rate',
                    'capacity_hours',
                    'efficiency_percentage',
                    'is_active',
                ]
            ]);
    }

    private function createInventoryTransaction($item, $warehouse, $quantity)
    {
        \App\Models\InventoryTransaction::factory()->create([
            'company_id' => $this->user->company_id,
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'transaction_type' => 'in',
            'quantity' => $quantity,
            'quantity_before' => 0,
            'quantity_after' => $quantity,
        ]);
    }
}
