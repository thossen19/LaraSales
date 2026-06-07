<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryAdjustmentRequest;
use App\Http\Resources\InventoryTransactionResource;
use App\Http\Resources\WarehouseResource;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function currentStock(Request $request)
    {
        $query = Item::where('company_id', $request->user()->company_id)
            ->where('is_stock_item', true)
            ->with(['inventoryTransactions' => function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id ?? 1)
                    ->latest()
                    ->take(1);
            }]);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        $stockData = $items->map(function ($item) use ($request) {
            $warehouseId = $request->warehouse_id ?? 1;
            $currentStock = $this->getCurrentStock($item->id, $warehouseId);
            $reorderLevel = $item->reorder_level;
            $status = 'normal';
            
            if ($currentStock <= 0) {
                $status = 'out_of_stock';
            } elseif ($currentStock <= $reorderLevel) {
                $status = 'low_stock';
            }

            return [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'category' => $item->category,
                'current_stock' => $currentStock,
                'reorder_level' => $reorderLevel,
                'reorder_quantity' => $item->reorder_quantity,
                'status' => $status,
                'unit_of_measure' => $item->unit_of_measure,
                'purchase_price' => $item->purchase_price,
                'sale_price' => $item->sale_price,
                'total_value' => $currentStock * $item->cost_price,
            ];
        });

        return response()->json($stockData);
    }

    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'warehouse'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return InventoryTransactionResource::collection($transactions);
    }

    public function adjust(InventoryAdjustmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $results = [];

            foreach ($request->items as $adjustment) {
                $item = Item::findOrFail($adjustment['item_id']);
                $warehouse = Warehouse::findOrFail($adjustment['warehouse_id']);

                $currentStock = $this->getCurrentStock($item->id, $warehouse->id);
                $newQuantity = $adjustment['new_quantity'];
                $quantity = $newQuantity - $currentStock;

                if ($quantity == 0) {
                    continue;
                }

                $transactionType = $quantity > 0 ? 'in' : 'out';
                $absQuantity = abs($quantity);
                $unitCost = $adjustment['unit_cost'] ?? $item->cost_price;

                InventoryTransaction::create([
                    'company_id' => $request->user()->company_id,
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'transaction_type' => 'adjustment',
                    'reference_type' => 'adjustment',
                    'reference_id' => null,
                    'quantity' => $absQuantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $absQuantity * $unitCost,
                    'quantity_before' => $currentStock,
                    'quantity_after' => $newQuantity,
                    'notes' => $adjustment['notes'] ?? 'Manual inventory adjustment',
                ]);

                $results[] = [
                    'item_id' => $item->id,
                    'item_code' => $item->code,
                    'item_name' => $item->name,
                    'warehouse_id' => $warehouse->id,
                    'warehouse_name' => $warehouse->name,
                    'quantity_before' => $currentStock,
                    'quantity_after' => $newQuantity,
                    'adjustment' => $quantity,
                ];
            }

            DB::commit();

            return response()->json([
                'message' => 'Inventory adjustment completed successfully',
                'adjustments' => $results,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to adjust inventory: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id,different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($request->item_id);
            $fromWarehouse = Warehouse::findOrFail($request->from_warehouse_id);
            $toWarehouse = Warehouse::findOrFail($request->to_warehouse_id);
            $quantity = $request->quantity;

            $fromStock = $this->getCurrentStock($item->id, $fromWarehouse->id);

            if ($fromStock < $quantity) {
                return response()->json(['message' => 'Insufficient stock in source warehouse'], Response::HTTP_BAD_REQUEST);
            }

            // Remove from source warehouse
            InventoryTransaction::create([
                'company_id' => $request->user()->company_id,
                'item_id' => $item->id,
                'warehouse_id' => $fromWarehouse->id,
                'transaction_type' => 'out',
                'reference_type' => 'transfer',
                'reference_id' => null,
                'quantity' => $quantity,
                'unit_cost' => $item->cost_price,
                'total_cost' => $quantity * $item->cost_price,
                'quantity_before' => $fromStock,
                'quantity_after' => $fromStock - $quantity,
                'notes' => $request->notes ?? "Transfer to {$toWarehouse->name}",
            ]);

            // Add to destination warehouse
            $toStock = $this->getCurrentStock($item->id, $toWarehouse->id);

            InventoryTransaction::create([
                'company_id' => $request->user()->company_id,
                'item_id' => $item->id,
                'warehouse_id' => $toWarehouse->id,
                'transaction_type' => 'in',
                'reference_type' => 'transfer',
                'reference_id' => null,
                'quantity' => $quantity,
                'unit_cost' => $item->cost_price,
                'total_cost' => $quantity * $item->cost_price,
                'quantity_before' => $toStock,
                'quantity_after' => $toStock + $quantity,
                'notes' => $request->notes ?? "Transfer from {$fromWarehouse->name}",
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Inventory transfer completed successfully',
                'item' => [
                    'id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                ],
                'from_warehouse' => [
                    'id' => $fromWarehouse->id,
                    'name' => $fromWarehouse->name,
                ],
                'to_warehouse' => [
                    'id' => $toWarehouse->id,
                    'name' => $toWarehouse->name,
                ],
                'quantity' => $quantity,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to transfer inventory: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function report(Request $request)
    {
        $query = InventoryTransaction::where('company_id', $request->user()->company_id);

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->with(['item', 'warehouse'])->get();

        $summary = [
            'total_transactions' => $transactions->count(),
            'total_in' => $transactions->where('transaction_type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('transaction_type', 'out')->sum('quantity'),
            'total_adjustments' => $transactions->where('transaction_type', 'adjustment')->count(),
            'total_transfers' => $transactions->where('transaction_type', 'transfer')->count(),
        ];

        return response()->json([
            'summary' => $summary,
            'transactions' => $transactions,
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
