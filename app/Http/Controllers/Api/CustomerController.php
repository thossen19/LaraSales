<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('company_id', $request->user()->company_id);

        if ($request->has('customer_type')) {
            $query->where('customer_type', $request->customer_type);
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

        $customers = $query->orderBy('name')->paginate(20);

        return CustomerResource::collection($customers);
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create([
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
            'customer_type' => $request->customer_type ?? 'individual',
            'credit_limit' => $request->credit_limit ?? 0,
            'current_balance' => 0,
            'payment_terms' => $request->payment_terms,
            'sales_account' => $request->sales_account,
            'receivable_account' => $request->receivable_account,
            'is_active' => $request->is_active ?? true,
            'notes' => $request->notes,
        ]);

        return new CustomerResource($customer);
    }

    public function show(Request $request, Customer $customer)
    {
        if ($customer->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new CustomerResource($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        if ($customer->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $customer->update([
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
            'customer_type' => $request->customer_type ?? $customer->customer_type,
            'credit_limit' => $request->credit_limit ?? $customer->credit_limit,
            'payment_terms' => $request->payment_terms,
            'sales_account' => $request->sales_account,
            'receivable_account' => $request->receivable_account,
            'is_active' => $request->is_active ?? $customer->is_active,
            'notes' => $request->notes,
        ]);

        return new CustomerResource($customer);
    }

    public function destroy(Request $request, Customer $customer)
    {
        if ($customer->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Check if customer has sales orders
        if ($customer->salesOrders()->exists()) {
            return response()->json(['message' => 'Cannot delete customer with existing sales orders'], Response::HTTP_BAD_REQUEST);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }

    public function balance(Request $request, Customer $customer)
    {
        if ($customer->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $totalSales = $customer->salesOrders()->sum('total_amount');
        $totalPaid = $customer->salesOrders()->sum('paid_amount');
        $currentBalance = $totalSales - $totalPaid;
        $creditLimit = $customer->credit_limit;
        $availableCredit = $creditLimit - $currentBalance;

        return response()->json([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'credit_limit' => $creditLimit,
            'total_sales' => $totalSales,
            'total_paid' => $totalPaid,
            'current_balance' => $currentBalance,
            'available_credit' => $availableCredit,
        ]);
    }

    public function orders(Request $request, Customer $customer)
    {
        if ($customer->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $query = $customer->salesOrders()->with(['items.item']);

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
