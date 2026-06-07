<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.item', 'createdBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(15);

        return PurchaseOrderResource::collection($purchaseOrders);
    }

    public function store(PurchaseOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::create([
                'company_id' => $request->user()->company_id,
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_date' => $request->expected_date,
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

                $unitPrice = $itemData['unit_price'] ?? $item->purchase_price;
                $quantity = $itemData['quantity'];
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $taxPercentage = $itemData['tax_percentage'] ?? 0;

                $lineSubtotal = $unitPrice * $quantity;
                $lineDiscount = $lineSubtotal * ($discountPercentage / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * ($taxPercentage / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
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

            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
            ]);

            DB::commit();

            return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.item', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create purchase order: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.item', 'createdBy']));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['message' => 'Cannot modify order that is not pending'], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_date' => $request->expected_date,
                'payment_terms' => $request->payment_terms,
                'delivery_address' => $request->delivery_address,
                'notes' => $request->notes,
            ]);

            $purchaseOrder->items()->delete();

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                $warehouse = Warehouse::findOrFail($itemData['warehouse_id']);

                $unitPrice = $itemData['unit_price'] ?? $item->purchase_price;
                $quantity = $itemData['quantity'];
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $taxPercentage = $itemData['tax_percentage'] ?? 0;

                $lineSubtotal = $unitPrice * $quantity;
                $lineDiscount = $lineSubtotal * ($discountPercentage / 100);
                $lineAfterDiscount = $lineSubtotal - $lineDiscount;
                $lineTax = $lineAfterDiscount * ($taxPercentage / 100);
                $lineTotal = $lineAfterDiscount + $lineTax;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
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

            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount - $purchaseOrder->paid_amount,
            ]);

            DB::commit();

            return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.item', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update purchase order: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if (!in_array($purchaseOrder->status, ['pending', 'partial'])) {
            return response()->json(['message' => 'Only pending or partially received orders can be received'], Response::HTTP_BAD_REQUEST);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $allReceived = true;

            foreach ($request->items as $itemData) {
                $purchaseOrderItem = PurchaseOrderItem::findOrFail($itemData['purchase_order_item_id']);
                $receivedQuantity = $itemData['received_quantity'];

                if ($receivedQuantity > ($purchaseOrderItem->quantity - $purchaseOrderItem->received_quantity)) {
                    return response()->json(['message' => 'Received quantity exceeds ordered quantity'], Response::HTTP_BAD_REQUEST);
                }

                $purchaseOrderItem->received_quantity += $receivedQuantity;
                $purchaseOrderItem->save();

                if ($purchaseOrderItem->received_quantity < $purchaseOrderItem->quantity) {
                    $allReceived = false;
                }

                // Add inventory transaction
                InventoryTransaction::create([
                    'company_id' => $purchaseOrder->company_id,
                    'item_id' => $purchaseOrderItem->item_id,
                    'warehouse_id' => $purchaseOrderItem->warehouse_id,
                    'transaction_type' => 'in',
                    'reference_type' => 'purchase',
                    'reference_id' => $purchaseOrder->id,
                    'quantity' => $receivedQuantity,
                    'unit_cost' => $purchaseOrderItem->unit_price,
                    'total_cost' => $receivedQuantity * $purchaseOrderItem->unit_price,
                    'quantity_before' => $this->getCurrentStock($purchaseOrderItem->item_id, $purchaseOrderItem->warehouse_id),
                    'quantity_after' => $this->getCurrentStock($purchaseOrderItem->item_id, $purchaseOrderItem->warehouse_id) + $receivedQuantity,
                    'notes' => 'Purchase order receipt',
                ]);
            }

            $purchaseOrder->update([
                'status' => $allReceived ? 'received' : 'partial',
            ]);

            DB::commit();

            return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.item', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to receive purchase order: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return response()->json(['message' => 'Cannot cancel received or already cancelled order'], Response::HTTP_BAD_REQUEST);
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.item', 'createdBy']));
    }

    public function destroy(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['message' => 'Cannot delete order that is not pending'], Response::HTTP_BAD_REQUEST);
        }

        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }

    public function report(Request $request)
    {
        $query = PurchaseOrder::where('company_id', $request->user()->company_id);

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchaseOrders = $query->with(['supplier', 'items.item'])->get();

        $totalAmount = $purchaseOrders->sum('total_amount');
        $paidAmount = $purchaseOrders->sum('paid_amount');
        $balanceAmount = $purchaseOrders->sum('balance_amount');

        return response()->json([
            'summary' => [
                'total_orders' => $purchaseOrders->count(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
            ],
            'orders' => $purchaseOrders,
        ]);
    }

    private function getCurrentStock($itemId, $warehouseId)
    {
        return InventoryTransaction::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->latest()
            ->first()
            ->quantity_after ?? 0;
    }
}
