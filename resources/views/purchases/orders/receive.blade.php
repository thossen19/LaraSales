@extends('layouts.app')
@section('title', 'Receive Purchase Order Items - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Receive Purchase Order Items</h2>
    <p class="mt-2 text-gray-600">Record goods received against purchase order #{{ $po->order_number }}.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('purchases.orders.receive', ['id' => $po->id]) }}">
@csrf

<!-- PO Header Summary -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Reference</label>
            <p class="text-sm text-gray-900 mt-1">{{ $po->order_number }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Supplier</label>
            <p class="text-sm text-gray-900 mt-1">{{ $po->supplier->name ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Order Date</label>
            <p class="text-sm text-gray-900 mt-1">{{ $po->order_date ? date('d/m/Y', strtotime($po->order_date)) : '' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Deliver Into Location</label>
            <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mt-1">
                @foreach($locations as $loc)
                    <option value="{{ $loc->loc_code }}" {{ ($po->location ?? '') == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                @endforeach
            </select>
        </div>
        @if($po->curr_code && $po->curr_code != 'USD')
        <div>
            <label class="block text-sm font-medium text-gray-700">Order Currency</label>
            <p class="text-sm text-gray-900 mt-1">{{ $po->curr_code }}</p>
        </div>
        @endif
        @if($po->supp_ref)
        <div>
            <label class="block text-sm font-medium text-gray-700">Supplier's Reference</label>
            <p class="text-sm text-gray-900 mt-1">{{ $po->supp_ref }}</p>
        </div>
        @endif
    </div>
</div>

<!-- Items Table -->
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Items to Receive</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ordered</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Units</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Received</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">This Delivery</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $total = 0; @endphp
                @foreach($po->items as $line)
                    @php
                        $qty_outstanding = $line->quantity - $line->received_quantity;
                        $line_total = $qty_outstanding * $line->unit_price;
                        $total += $line_total;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $line->item->code ?? '' }}</td>
                        <td class="px-4 py-3">
                            @if($qty_outstanding > 0)
                                <input type="text" name="desc_{{ $line->id }}" value="{{ $line->description }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @else
                                <span class="text-sm text-gray-700">{{ $line->description }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $line->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $line->item->unit_of_measure ?? 'each' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $line->received_quantity }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ $qty_outstanding }}</td>
                        <td class="px-4 py-3">
                            @if($qty_outstanding > 0)
                                <input type="number" name="receive_qty_{{ $line->id }}" value="{{ $qty_outstanding }}" min="0" max="{{ $qty_outstanding }}" step="1" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @else
                                <span class="text-sm text-gray-500 text-right block">0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($line->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ number_format($line_total, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="bg-gray-100">
                    <td colspan="7" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Sub-total</td>
                    <td colspan="2" class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($total, 2) }}</td>
                </tr>
                <tr class="bg-gray-100">
                    <td colspan="7" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Amount Total</td>
                    <td colspan="2" class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Date:</label>
            <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Reference:</label>
            <input type="text" name="ref" value="GRN-{{ date('Ymd') }}-{{ $po->id }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-center space-x-4">
    <button type="submit" name="Update" value="1" class="px-6 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition">Update</button>
    <button type="submit" name="ProcessGoodsReceived" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Process Receive Items</button>
</div>

</form>
@endsection
