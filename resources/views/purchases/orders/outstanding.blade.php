@extends('layouts.app')
@section('title', 'Search Outstanding Purchase Orders - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Search Outstanding Purchase Orders</h2>
    <p class="mt-2 text-gray-600">View and manage outstanding purchase orders.</p>
</div>

<form method="GET" action="{{ route('purchases.orders.outstanding') }}">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">#:</label>
                <input type="text" name="order_no" value="{{ $order_no }}" placeholder="Order #" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From:</label>
                <input type="date" name="from_date" value="{{ $from_date }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To:</label>
                <input type="date" name="to_date" value="{{ $to_date }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location:</label>
                <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- All --</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->loc_code }}" {{ $location == $l->loc_code ? 'selected' : '' }}>{{ $l->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item:</label>
                <select name="item_code" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- All --</option>
                    @foreach($items as $it)
                        <option value="{{ $it->code }}" {{ $item_code == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                <select name="supplier_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- All --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-5">
                <button type="submit" name="SearchOrders" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Search</button>
            </div>
        </div>
    </div>
</form>

@if(count($orders) > 0)
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if(!empty($overdue_ids))
        <div class="px-6 py-3 bg-yellow-50 border-b border-yellow-200 text-yellow-800 text-sm">
            Marked orders have overdue items.
        </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        @if(!$location)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier's Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Date</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Currency</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Order Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 {{ in_array($order->id, $overdue_ids) ? 'bg-yellow-50' : '' }}">
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('purchases.orders.create') }}?ModifyOrderNumber={{ $order->id }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">{{ $order->id }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $order->supplier->name ?? 'N/A' }}</td>
                        @if(!$location)
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $order->location ?? 'N/A' }}</td>
                        @endif
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $order->supp_ref ?? '' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $order->order_date ? date('d/m/Y', strtotime($order->order_date)) : '' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $order->curr_code ?? ($order->supplier->curr_code ?? 'USD') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('purchases.orders.create') }}?ModifyOrderNumber={{ $order->id }}" class="text-indigo-600 hover:text-indigo-900 inline-block p-1" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('purchases.orders.receive') }}?id={{ $order->id }}" class="text-green-600 hover:text-green-900 inline-block p-1" title="Receive">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('purchases.orders.print', $order->id) }}" class="text-gray-600 hover:text-gray-900 inline-block p-1" title="Print" target="_blank">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
@else
    <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500">
        No purchase orders found matching your criteria.
    </div>
@endif
@endsection
