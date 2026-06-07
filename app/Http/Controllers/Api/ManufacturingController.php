<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillOfMaterialsRequest;
use App\Http\Requests\ProductionOrderRequest;
use App\Http\Resources\BillOfMaterialsResource;
use App\Http\Resources\ProductionOrderResource;
use App\Http\Resources\WorkCenterResource;
use App\Models\BillOfMaterials;
use App\Models\BomItem;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\ProductionOrder;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends Controller
{
    // Bill of Materials
    public function bomIndex(Request $request)
    {
        $query = BillOfMaterials::with(['item', 'bomItems.componentItem', 'createdBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $boms = $query->orderBy('created_at', 'desc')->paginate(15);

        return BillOfMaterialsResource::collection($boms);
    }

    public function bomStore(BillOfMaterialsRequest $request)
    {
        try {
            DB::beginTransaction();

            $bom = BillOfMaterials::create([
                'company_id' => $request->user()->company_id,
                'item_id' => $request->item_id,
                'version' => $request->version ?? '1.0',
                'status' => $request->status ?? 'active',
                'standard_cost' => $request->standard_cost ?? 0,
                'notes' => $request->notes,
                'is_default' => $request->is_default ?? false,
                'effective_date' => $request->effective_date ?? now(),
                'created_by' => $request->user()->id,
            ]);

            foreach ($request->items as $index => $component) {
                BomItem::create([
                    'bill_of_materials_id' => $bom->id,
                    'component_item_id' => $component['component_item_id'],
                    'quantity' => $component['quantity'],
                    'unit_of_measure' => $component['unit_of_measure'],
                    'scrap_percentage' => $component['scrap_percentage'] ?? 0,
                    'notes' => $component['notes'] ?? null,
                    'sequence' => $index + 1,
                ]);
            }

            DB::commit();

            return new BillOfMaterialsResource($bom->load(['item', 'bomItems.componentItem', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create BOM: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function bomShow(Request $request, BillOfMaterials $bom)
    {
        if ($bom->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new BillOfMaterialsResource($bom->load(['item', 'bomItems.componentItem', 'createdBy']));
    }

    public function bomUpdate(BillOfMaterialsRequest $request, BillOfMaterials $bom)
    {
        if ($bom->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();

            $bom->update([
                'item_id' => $request->item_id,
                'version' => $request->version ?? $bom->version,
                'status' => $request->status ?? $bom->status,
                'standard_cost' => $request->standard_cost ?? $bom->standard_cost,
                'notes' => $request->notes,
                'is_default' => $request->is_default ?? $bom->is_default,
                'effective_date' => $request->effective_date ?? $bom->effective_date,
            ]);

            $bom->bomItems()->delete();

            foreach ($request->items as $index => $component) {
                BomItem::create([
                    'bill_of_materials_id' => $bom->id,
                    'component_item_id' => $component['component_item_id'],
                    'quantity' => $component['quantity'],
                    'unit_of_measure' => $component['unit_of_measure'],
                    'scrap_percentage' => $component['scrap_percentage'] ?? 0,
                    'notes' => $component['notes'] ?? null,
                    'sequence' => $index + 1,
                ]);
            }

            DB::commit();

            return new BillOfMaterialsResource($bom->load(['item', 'bomItems.componentItem', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update BOM: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Production Orders
    public function productionIndex(Request $request)
    {
        $query = ProductionOrder::with(['item', 'warehouse', 'createdBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(15);

        return ProductionOrderResource::collection($orders);
    }

    public function productionStore(ProductionOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = ProductionOrder::create([
                'company_id' => $request->user()->company_id,
                'item_id' => $request->item_id,
                'warehouse_id' => $request->warehouse_id,
                'order_date' => $request->order_date,
                'start_date' => $request->start_date,
                'finish_date' => $request->finish_date,
                'status' => 'planned',
                'quantity_planned' => $request->quantity_planned,
                'standard_cost' => $request->standard_cost ?? 0,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            DB::commit();

            return new ProductionOrderResource($order->load(['item', 'warehouse', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create production order: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function productionShow(Request $request, ProductionOrder $order)
    {
        if ($order->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new ProductionOrderResource($order->load(['item', 'warehouse', 'createdBy']));
    }

    public function productionRelease(Request $request, ProductionOrder $order)
    {
        if ($order->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($order->status !== 'planned') {
            return response()->json(['message' => 'Only planned orders can be released'], Response::HTTP_BAD_REQUEST);
        }

        $order->update([
            'status' => 'released',
            'start_date' => $order->start_date ?? now(),
        ]);

        return new ProductionOrderResource($order->load(['item', 'warehouse', 'createdBy']));
    }

    public function productionStart(Request $request, ProductionOrder $order)
    {
        if ($order->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($order->status !== 'released') {
            return response()->json(['message' => 'Only released orders can be started'], Response::HTTP_BAD_REQUEST);
        }

        // Check material availability
        $bom = BillOfMaterials::where('item_id', $order->item_id)
            ->active()
            ->default()
            ->first();

        if ($bom) {
            foreach ($bom->bomItems as $bomItem) {
                $currentStock = $this->getCurrentStock($bomItem->component_item_id, $order->warehouse_id);
                $requiredQuantity = $bomItem->effective_quantity * $order->quantity_planned;

                if ($currentStock < $requiredQuantity) {
                    return response()->json([
                        'message' => 'Insufficient materials',
                        'shortage' => [
                            'item' => $bomItem->componentItem->name,
                            'required' => $requiredQuantity,
                            'available' => $currentStock,
                            'shortage' => $requiredQuantity - $currentStock,
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Consume materials
            foreach ($bom->bomItems as $bomItem) {
                $requiredQuantity = $bomItem->effective_quantity * $order->quantity_planned;
                $currentStock = $this->getCurrentStock($bomItem->component_item_id, $order->warehouse_id);

                InventoryTransaction::create([
                    'company_id' => $order->company_id,
                    'item_id' => $bomItem->component_item_id,
                    'warehouse_id' => $order->warehouse_id,
                    'transaction_type' => 'out',
                    'reference_type' => 'production',
                    'reference_id' => $order->id,
                    'quantity' => $requiredQuantity,
                    'unit_cost' => $bomItem->componentItem->cost_price,
                    'total_cost' => $requiredQuantity * $bomItem->componentItem->cost_price,
                    'quantity_before' => $currentStock,
                    'quantity_after' => $currentStock - $requiredQuantity,
                    'notes' => "Material consumption for production order {$order->order_number}",
                ]);
            }
        }

        $order->update(['status' => 'in_progress']);

        return new ProductionOrderResource($order->load(['item', 'warehouse', 'createdBy']));
    }

    public function productionComplete(Request $request, ProductionOrder $order)
    {
        $request->validate([
            'quantity_produced' => 'required|integer|min:1|max:' . $order->remaining_quantity,
            'quantity_scrapped' => 'nullable|integer|min:0',
        ]);

        if ($order->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($order->status !== 'in_progress') {
            return response()->json(['message' => 'Only in-progress orders can be completed'], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $quantityProduced = $request->quantity_produced;
            $quantityScrapped = $request->quantity_scrapped ?? 0;

            // Add finished goods to inventory
            $currentStock = $this->getCurrentStock($order->item_id, $order->warehouse_id);

            InventoryTransaction::create([
                'company_id' => $order->company_id,
                'item_id' => $order->item_id,
                'warehouse_id' => $order->warehouse_id,
                'transaction_type' => 'in',
                'reference_type' => 'production',
                'reference_id' => $order->id,
                'quantity' => $quantityProduced,
                'unit_cost' => $order->standard_cost,
                'total_cost' => $quantityProduced * $order->standard_cost,
                'quantity_before' => $currentStock,
                'quantity_after' => $currentStock + $quantityProduced,
                'notes' => "Production completion for order {$order->order_number}",
            ]);

            $order->update([
                'quantity_produced' => $order->quantity_produced + $quantityProduced,
                'quantity_scrapped' => $order->quantity_scrapped + $quantityScrapped,
                'status' => $order->remaining_quantity <= 0 ? 'completed' : 'in_progress',
                'finish_date' => $order->remaining_quantity <= 0 ? now() : $order->finish_date,
            ]);

            DB::commit();

            return new ProductionOrderResource($order->load(['item', 'warehouse', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to complete production: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Work Centers
    public function workCenters(Request $request)
    {
        $workCenters = WorkCenter::where('company_id', $request->user()->company_id)
            ->orderBy('code')
            ->get();

        return WorkCenterResource::collection($workCenters);
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
