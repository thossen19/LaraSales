@extends('layouts.app')
@section('title', 'Delivery Against Sales Orders - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Delivery Against Sales Orders</h2>
    <p class="mt-1 text-sm text-gray-500">Search outstanding sales orders and process deliveries.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if($deliveryOrderId && isset($order))
    {{-- Delivery Entry Mode --}}
    <form method="POST" action="{{ route('sales.delivery.from-order') }}">
    @csrf
    <input type="hidden" name="order_id" value="{{ $deliveryOrderId }}">
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-truck mr-2"></i>Deliver Items for a Sales Order</h3>
            <div class="text-sm text-indigo-200">
                Order: <span class="font-semibold text-white">{{ $order->order_number }}</span> |
                Customer: <span class="font-semibold text-white">{{ $order->customer->name ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Date</label>
                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deliver from Location</label>
                    <select name="Location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->loc_code }}" {{ isset($order) && $order->location == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Company</label>
                    <select name="ship_via" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($shippers as $sh)
                            <option value="{{ $sh->shipper_id }}" {{ isset($order) && $order->ship_via == $sh->shipper_id ? 'selected' : '' }}>{{ $sh->shipper_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-900 mb-3">Order Items</h4>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordered</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Previous</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">To Deliver</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($order->lineItems as $li)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $li->item_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $li->description }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li->quantity, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format(0, 4) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="text" name="qty[{{ $li->id }}]" value="{{ number_format($li->quantity, 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li->unit_price, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">{{ number_format($li->quantity * $li->unit_price, 4) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-3 text-sm text-gray-500 text-center">No items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Notes</label>
                <textarea name="Comments" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $order->customer_notes ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-4 mt-6">
        <button type="submit" name="CancelDelivery" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Back to Orders</button>
        <button type="submit" name="ProcessDelivery" value="1" class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-md hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm"><i class="fas fa-truck mr-2"></i>Process Delivery</button>
    </div>
    </form>
@else
    {{-- Search Mode --}}
    <form method="GET" action="{{ route('sales.delivery.from-order') }}">
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-search mr-2"></i>Search Outstanding Sales Orders</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                    <input type="text" name="order_no" value="{{ $searchOrderNo }}" placeholder="Partial or full #" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $searchCustomer == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->loc_code }}" {{ $searchLocation == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date From</label>
                    <input type="date" name="from_date" value="{{ $searchFrom }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="to_date" value="{{ $searchTo }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-search mr-2"></i>Search</button>
                <a href="{{ route('sales.delivery.from-order') }}" class="px-5 py-2 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Show All</a>
            </div>
        </div>
    </div>
    </form>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Outstanding Sales Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Due Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $so)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $so->order_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $so->internal_notes ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $so->customer->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $so->order_date ? $so->order_date->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $so->delivery_date ? $so->delivery_date->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">${{ number_format($so->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('sales.delivery.from-order') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="select_order" value="{{ $so->id }}">
                                    <button type="submit" title="Delivery" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm mx-1"><i class="fas fa-truck"></i></button>
                                </form>
                                <a href="{{ route('sales.orders.edit', $so) }}" title="Edit" class="text-amber-600 hover:text-amber-900 font-medium text-sm mx-1"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('sales.orders.show', $so) . '?print=1' }}" title="View" target="_blank" class="text-gray-600 hover:text-gray-900 font-medium text-sm mx-1"><i class="fas fa-print"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-sm text-gray-500 text-center">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                                No outstanding sales orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->count() > 0)
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-500">
                Showing {{ $orders->count() }} order(s)
            </div>
        @endif
    </div>
@endif
@endsection
