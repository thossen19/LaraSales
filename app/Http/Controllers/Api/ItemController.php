<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::where('company_id', $request->user()->company_id);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('is_stock_item')) {
            $query->where('is_stock_item', $request->boolean('is_stock_item'));
        }

        if ($request->has('is_service')) {
            $query->where('is_service', $request->boolean('is_service'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        return ItemResource::collection($items);
    }

    public function store(ItemRequest $request)
    {
        $item = Item::create([
            'company_id' => $request->user()->company_id,
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'unit_of_measure' => $request->unit_of_measure,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'barcode' => $request->barcode,
            'reorder_level' => $request->reorder_level,
            'reorder_quantity' => $request->reorder_quantity,
            'is_active' => $request->is_active ?? true,
            'is_stock_item' => $request->is_stock_item ?? true,
            'is_service' => $request->is_service ?? false,
            'sales_account' => $request->sales_account,
            'purchase_account' => $request->purchase_account,
            'inventory_account' => $request->inventory_account,
            'cogs_account' => $request->cogs_account,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/items', $imageName);
            $item->update(['image' => $imageName]);
        }

        return new ItemResource($item);
    }

    public function show(Request $request, Item $item)
    {
        if ($item->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new ItemResource($item);
    }

    public function update(ItemRequest $request, Item $item)
    {
        if ($item->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $item->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'unit_of_measure' => $request->unit_of_measure,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'barcode' => $request->barcode,
            'reorder_level' => $request->reorder_level,
            'reorder_quantity' => $request->reorder_quantity,
            'is_active' => $request->is_active ?? $item->is_active,
            'is_stock_item' => $request->is_stock_item ?? $item->is_stock_item,
            'is_service' => $request->is_service ?? $item->is_service,
            'sales_account' => $request->sales_account,
            'purchase_account' => $request->purchase_account,
            'inventory_account' => $request->inventory_account,
            'cogs_account' => $request->cogs_account,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/items', $imageName);
            $item->update(['image' => $imageName]);
        }

        return new ItemResource($item);
    }

    public function destroy(Request $request, Item $item)
    {
        if ($item->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Check if item has transactions
        if ($item->salesOrderItems()->exists() || $item->purchaseOrderItems()->exists()) {
            return response()->json(['message' => 'Cannot delete item with existing transactions'], Response::HTTP_BAD_REQUEST);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }

    public function categories(Request $request)
    {
        $categories = Item::where('company_id', $request->user()->company_id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        return response()->json($categories);
    }

    public function stockLevels(Request $request)
    {
        $items = Item::where('company_id', $request->user()->company_id)
            ->where('is_stock_item', true)
            ->with(['inventoryTransactions' => function ($query) {
                $query->latest()->take(1);
            }])
            ->get();

        $stockLevels = $items->map(function ($item) {
            $currentStock = $item->inventoryTransactions->first()?->quantity_after ?? 0;
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
            ];
        });

        return response()->json($stockLevels);
    }
}
