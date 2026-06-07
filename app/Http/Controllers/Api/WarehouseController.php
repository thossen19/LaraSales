<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::where('company_id', $request->user()->company_id)
            ->orderBy('name')
            ->get();

        return WarehouseResource::collection($warehouses);
    }

    public function store(WarehouseRequest $request)
    {
        $warehouse = Warehouse::create([
            'company_id' => $request->user()->company_id,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'contact_person' => $request->contact_person,
            'is_active' => $request->is_active ?? true,
            'is_default' => $request->is_default ?? false,
            'notes' => $request->notes,
        ]);

        if ($warehouse->is_default) {
            Warehouse::where('company_id', $request->user()->company_id)
                ->where('id', '!=', $warehouse->id)
                ->update(['is_default' => false]);
        }

        return new WarehouseResource($warehouse);
    }

    public function show(Request $request, Warehouse $warehouse)
    {
        if ($warehouse->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new WarehouseResource($warehouse);
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        if ($warehouse->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $warehouse->update([
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'contact_person' => $request->contact_person,
            'is_active' => $request->is_active ?? $warehouse->is_active,
            'is_default' => $request->is_default ?? $warehouse->is_default,
            'notes' => $request->notes,
        ]);

        if ($warehouse->is_default) {
            Warehouse::where('company_id', $request->user()->company_id)
                ->where('id', '!=', $warehouse->id)
                ->update(['is_default' => false]);
        }

        return new WarehouseResource($warehouse);
    }

    public function destroy(Request $request, Warehouse $warehouse)
    {
        if ($warehouse->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Check if warehouse has transactions
        if ($warehouse->inventoryTransactions()->exists()) {
            return response()->json(['message' => 'Cannot delete warehouse with existing transactions'], Response::HTTP_BAD_REQUEST);
        }

        if ($warehouse->is_default) {
            return response()->json(['message' => 'Cannot delete default warehouse'], Response::HTTP_BAD_REQUEST);
        }

        $warehouse->delete();

        return response()->json(['message' => 'Warehouse deleted successfully']);
    }

    public function setDefault(Request $request, Warehouse $warehouse)
    {
        if ($warehouse->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        Warehouse::where('company_id', $request->user()->company_id)
            ->update(['is_default' => false]);

        $warehouse->update(['is_default' => true]);

        return new WarehouseResource($warehouse);
    }
}
