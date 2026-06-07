<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::where('company_id', $request->user()->company_id);

        if ($request->has('supplier_type')) {
            $query->where('supplier_type', $request->supplier_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(20);

        return SupplierResource::collection($suppliers);
    }

    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create([
            'company_id' => $request->user()->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'fax' => $request->fax,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'tax_id' => $request->tax_id,
            'supplier_type' => $request->supplier_type ?? 'individual',
            'credit_limit' => $request->credit_limit ?? 0,
            'current_balance' => 0,
            'payment_terms' => $request->payment_terms,
            'purchase_account' => $request->purchase_account,
            'payable_account' => $request->payable_account,
            'is_active' => $request->is_active ?? true,
            'notes' => $request->notes,
        ]);

        return new SupplierResource($supplier);
    }

    public function show(Request $request, Supplier $supplier)
    {
        if ($supplier->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new SupplierResource($supplier);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        if ($supplier->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $supplier->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'fax' => $request->fax,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'tax_id' => $request->tax_id,
            'supplier_type' => $request->supplier_type ?? $supplier->supplier_type,
            'credit_limit' => $request->credit_limit ?? $supplier->credit_limit,
            'payment_terms' => $request->payment_terms,
            'purchase_account' => $request->purchase_account,
            'payable_account' => $request->payable_account,
            'is_active' => $request->is_active ?? $supplier->is_active,
            'notes' => $request->notes,
        ]);

        return new SupplierResource($supplier);
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        if ($supplier->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Check if supplier has purchase orders
        if ($supplier->purchaseOrders()->exists()) {
            return response()->json(['message' => 'Cannot delete supplier with existing purchase orders'], Response::HTTP_BAD_REQUEST);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }

    public function balance(Request $request, Supplier $supplier)
    {
        if ($supplier->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $totalPurchases = $supplier->purchaseOrders()->sum('total_amount');
        $totalPaid = $supplier->purchaseOrders()->sum('paid_amount');
        $currentBalance = $totalPurchases - $totalPaid;
        $creditLimit = $supplier->credit_limit;
        $availableCredit = $creditLimit - $currentBalance;

        return response()->json([
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'credit_limit' => $creditLimit,
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPaid,
            'current_balance' => $currentBalance,
            'available_credit' => $availableCredit,
        ]);
    }

    public function orders(Request $request, Supplier $supplier)
    {
        if ($supplier->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $query = $supplier->purchaseOrders()->with(['items.item']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(15);

        return response()->json($orders);
    }
}
