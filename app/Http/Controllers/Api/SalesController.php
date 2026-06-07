<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesOrderRequest;
use App\Http\Resources\SalesOrderResource;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::with(['customer', 'items.item', 'createdBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $salesOrders = $query->orderBy('created_at', 'desc')->paginate(15);

        return SalesOrderResource::collection($salesOrders);
    }

    public function store(SalesOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $salesOrder = SalesOrder::create([
                'company_id' => $request->user()->company_id,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'status' => 'pending',
                'payment_terms' => $request->payment_terms,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $warehouse = Warehouse::findOrFail($itemData['warehouse_id']);

                $unitPrice = $itemData['unit_price'] ?? $item->sale_price;
                $quantity = $itemData['quantity'];
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $taxPercentage = $itemData['tax_percentage'] ?? 0;

                $lineSubtotal = $unitPrice * $quantity;
                $lineDiscount = $lineSubtotal * ($discountPercentage / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * ($taxPercentage / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'description' => $itemData['description'] ?? $item->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $lineDiscount,
                    'tax_percentage' => $taxPercentage,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ]);

                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
                $discountAmount += $lineDiscount;
            }

            $shippingAmount = $request->shipping_amount ?? 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

            $salesOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
            ]);

            DB::commit();

            return new SalesOrderResource($salesOrder->load(['customer', 'items.item', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create sales order: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new SalesOrderResource($salesOrder->load(['customer', 'items.item', 'createdBy']));
    }

    public function update(SalesOrderRequest $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($salesOrder->status !== 'pending') {
            return response()->json(['message' => 'Cannot modify order that is not pending'], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $salesOrder->update([
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'payment_terms' => $request->payment_terms,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
            ]);

            $salesOrder->items()->delete();

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $warehouse = Warehouse::findOrFail($itemData['warehouse_id']);

                $unitPrice = $itemData['unit_price'] ?? $item->sale_price;
                $quantity = $itemData['quantity'];
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $taxPercentage = $itemData['tax_percentage'] ?? 0;

                $lineSubtotal = $unitPrice * $quantity;
                $lineDiscount = $lineSubtotal * ($discountPercentage / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * ($taxPercentage / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'description' => $itemData['description'] ?? $item->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $lineDiscount,
                    'tax_percentage' => $taxPercentage,
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                ]);

                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
                $discountAmount += $lineDiscount;
            }

            $shippingAmount = $request->shipping_amount ?? 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

            $salesOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount - $salesOrder->paid_amount,
            ]);

            DB::commit();

            return new SalesOrderResource($salesOrder->load(['customer', 'items.item', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update sales order: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function confirm(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($salesOrder->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be confirmed'], Response::HTTP_BAD_REQUEST);
        }

        $salesOrder->update(['status' => 'confirmed']);

        return new SalesOrderResource($salesOrder->load(['customer', 'items.item', 'createdBy']));
    }

    public function cancel(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if (in_array($salesOrder->status, ['delivered', 'cancelled'])) {
            return response()->json(['message' => 'Cannot cancel delivered or already cancelled order'], Response::HTTP_BAD_REQUEST);
        }

        $salesOrder->update(['status' => 'cancelled']);

        return new SalesOrderResource($salesOrder->load(['customer', 'items.item', 'createdBy']));
    }

    public function destroy(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($salesOrder->status !== 'pending') {
            return response()->json(['message' => 'Cannot delete order that is not pending'], Response::HTTP_BAD_REQUEST);
        }

        $salesOrder->delete();

        return response()->json(['message' => 'Sales order deleted successfully']);
    }
}
