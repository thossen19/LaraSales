<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $customer;
    protected $item;
    protected $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $this->item = Item::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $this->warehouse = Warehouse::factory()->create([
            'company_id' => $this->user->company_id,
        ]);
    }

    public function test_can_list_sales_orders()
    {
        $salesOrder = SalesOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson('/api/sales-orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'order_number',
                        'order_date',
                        'status',
                        'total_amount',
                        'customer',
                    ]
                ]
            ]);
    }

    public function test_can_create_sales_order()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'order_date' => now()->toDateString(),
            'delivery_date' => now()->addDays(7)->toDateString(),
            'payment_terms' => 'Net 30',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'warehouse_id' => $this->warehouse->id,
                    'quantity' => 10,
                    'unit_price' => 100,
                    'discount_percentage' => 10,
                    'tax_percentage' => 15,
                ]
            ],
        ];

        $response = $this->postJson('/api/sales-orders', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'order_number',
                'order_date',
                'status',
                'total_amount',
                'customer',
                'items',
            ]);

        $this->assertDatabaseHas('sales_orders', [
            'customer_id' => $this->customer->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_show_sales_order()
    {
        $salesOrder = SalesOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson("/api/sales-orders/{$salesOrder->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $salesOrder->id,
                'order_number' => $salesOrder->order_number,
            ]);
    }

    public function test_can_confirm_sales_order()
    {
        $salesOrder = SalesOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/sales-orders/{$salesOrder->id}/confirm");

        $response->assertStatus(200);

        $this->assertDatabaseHas('sales_orders', [
            'id' => $salesOrder->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_can_cancel_sales_order()
    {
        $salesOrder = SalesOrder::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/sales-orders/{$salesOrder->id}/cancel");

        $response->assertStatus(200);

        $this->assertDatabaseHas('sales_orders', [
            'id' => $salesOrder->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_access_other_company_sales_order()
    {
        $otherUser = User::factory()->create();
        $salesOrder = SalesOrder::factory()->create([
            'company_id' => $otherUser->company_id,
        ]);

        $response = $this->getJson("/api/sales-orders/{$salesOrder->id}");

        $response->assertStatus(403);
    }

    public function test_sales_order_validation()
    {
        $response = $this->postJson('/api/sales-orders', [
            'customer_id' => null,
            'items' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_id', 'items']);
    }
}
