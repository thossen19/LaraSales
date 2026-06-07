@extends('layouts.app')
@section('title', 'Fixed Asset Purchase Invoice Entry')
@section('content')
@php
use App\Models\Dimension;
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Fixed Asset Purchase Invoice Entry</h2>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif
@if(session('warning'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">{{ session('warning') }}</div>
@endif

<form method="POST" action="{{ route('fixed-assets.purchase') }}">
@csrf

{{-- Header --}}
<table class="w-full mb-4" cellpadding="4">
    <tr>
        <td class="w-1/3 align-top">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                <select name="supplier_id" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $cart['supplier_id'] == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date:</label>
                <input type="date" name="invoice_date" value="{{ $cart['invoice_date'] ?? date('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date:</label>
                <input type="date" name="due_date" value="{{ $cart['due_date'] ?? date('Y-m-d', strtotime('+30 days')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference:</label>
                <input type="text" name="reference" value="{{ $cart['reference'] ?? '' }}" maxlength="20"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </td>
        <td class="w-1/3 align-top px-4">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Reference:</label>
                <input type="text" name="supp_reference" value="{{ $cart['supp_reference'] ?? '' }}" maxlength="60"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            @if(($cart['curr_code'] ?? 'USD') != 'USD')
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Currency:</label>
                    <p class="text-sm font-semibold py-2">{{ $cart['curr_code'] ?? 'USD' }}</p>
                </div>
            @endif
            @if(\App\Models\Setting::getSetting('use_dimension', 1, 0))
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimension:</label>
                    <select name="dimension_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Default</option>
                        @foreach($dimensions as $d)
                            <option value="{{ $d->id }}" {{ ($cart['dimension_id'] ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if(\App\Models\Setting::getSetting('use_dimension', 1, 0) == 2)
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 2:</label>
                    <select name="dimension2_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Default</option>
                        @foreach($dimensions as $d)
                            <option value="{{ $d->id }}" {{ ($cart['dimension2_id'] ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </td>
        <td class="w-1/3 align-top">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Receive Into:</label>
                <select name="location" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a location</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->loc_code }}" {{ $cart['location'] == $l->loc_code ? 'selected' : '' }}>{{ $l->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deliver to:</label>
                <textarea name="delivery_address" rows="4"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $cart['delivery_address'] ?? '' }}</textarea>
            </div>
        </td>
    </tr>
</table>

{{-- Items --}}
<h3 class="text-lg font-semibold text-gray-800 mb-2">Order Items</h3>

<div class="bg-white shadow rounded-lg overflow-hidden mb-4">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Description</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Line Total</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($cart['items'] as $line_no => $li)
                @if($cart['edit_line'] == $line_no)
                    {{-- Edit row --}}
                    <tr class="bg-yellow-50">
                        <td class="px-4 py-2"><input type="hidden" name="stock_id" value="{{ $cart['stock_id'] ?? $li['stock_id'] }}"><span class="text-sm">{{ $cart['stock_id'] ?? $li['stock_id'] }}</span></td>
                        <td class="px-4 py-2"><input type="text" name="item_description" value="{{ $cart['item_description'] ?? $li['item_description'] }}" class="w-full border border-gray-300 rounded px-2 py-1 text-sm"></td>
                        <td class="px-4 py-2 text-center text-sm">1 <input type="hidden" name="qty" value="1"></td>
                        <td class="px-4 py-2 text-center text-sm">{{ $li['unit'] ?? 'each' }}</td>
                        <td class="px-4 py-2"><input type="text" name="price" value="{{ $cart['price'] ?? $li['price'] }}" class="w-24 text-right border border-gray-300 rounded px-2 py-1 text-sm"></td>
                        <td class="px-4 py-2 text-right text-sm">{{ number_format(($cart['price'] ?? $li['price']) * 1, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <button type="submit" name="UpdateLine" class="text-indigo-600 hover:text-indigo-900 text-sm mr-2">Update</button>
                            <button type="submit" name="CancelUpdate" class="text-gray-600 hover:text-gray-900 text-sm">Cancel</button>
                            <input type="hidden" name="line_no" value="{{ $line_no }}">
                        </td>
                    </tr>
                @else
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">{{ $li['stock_id'] }}</td>
                        <td class="px-4 py-2 text-sm">{{ $li['item_description'] }}</td>
                        <td class="px-4 py-2 text-sm text-center">{{ $li['quantity'] }}</td>
                        <td class="px-4 py-2 text-sm text-center">{{ $li['unit'] ?? 'each' }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format($li['price'], 4) }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format($li['quantity'] * $li['price'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-center">
                            <button type="submit" name="Edit{{ $line_no }}" value="1" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                            <button type="submit" name="Delete{{ $line_no }}" value="1" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                @endif
            @endforeach

            {{-- Add new item row --}}
            @if($cart['edit_line'] == -1)
                <tr class="bg-gray-50">
                    <td class="px-4 py-2">
                        <select name="stock_id" onchange="this.form.submit()"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select item</option>
                            @foreach($fixed_assets as $fa)
                                <option value="{{ $fa->code }}" {{ ($cart['stock_id'] ?? '') == $fa->code ? 'selected' : '' }}>{{ $fa->code }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-2">
                        <input type="text" name="item_description" value="{{ $cart['item_description'] ?? '' }}" placeholder="Description"
                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-2 text-center text-sm">1 <input type="hidden" name="qty" value="1"></td>
                    <td class="px-4 py-2 text-center text-sm">{{ $cart['_unit'] ?? 'each' }}</td>
                    <td class="px-4 py-2">
                        <input type="text" name="price" value="{{ $cart['price'] ?? '0' }}"
                               class="w-24 text-right border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-2 text-right text-sm">{{ number_format(($cart['price'] ?? 0) * 1, 2) }}</td>
                    <td class="px-4 py-2 text-center">
                        <button type="submit" name="EnterLine" class="px-3 py-1 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 focus:outline-none">Add Item</button>
                    </td>
                </tr>
            @endif

            {{-- Totals --}}
            @php
                $subtotal = 0;
                foreach ($cart['items'] as $li) { $subtotal += $li['quantity'] * $li['price']; }
            @endphp
            @if(count($cart['items']) > 0)
                <tr class="bg-gray-50 font-medium">
                    <td colspan="4" class="px-4 py-2 text-sm text-right">Sub-total</td>
                    <td class="px-4 py-2 text-sm text-right"></td>
                    <td class="px-4 py-2 text-sm text-right">{{ number_format($subtotal, 2) }}</td>
                    <td></td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                    <td colspan="4" class="px-4 py-2 text-sm text-right">Amount Total</td>
                    <td class="px-4 py-2 text-sm text-right"></td>
                    <td class="px-4 py-2 text-sm text-right">{{ number_format($subtotal, 2) }}</td>
                    <td class="px-4 py-2 text-center">
                        <button type="submit" name="update" value="1" class="text-indigo-600 hover:text-indigo-900 text-sm">Update</button>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Footer --}}
<table class="w-full mb-4" cellpadding="4">
    <tr>
        <td class="w-1/2">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment:</label>
                <select name="cash_account_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Delayed</option>
                    @foreach($cash_accounts as $ca)
                        <option value="{{ $ca->id }}" {{ ($cart['cash_account_id'] ?? '') == $ca->id ? 'selected' : '' }}>{{ $ca->bank_name }} - {{ $ca->bank_account_name }} ({{ $ca->bank_curr_code ?? 'USD' }})</option>
                    @endforeach
                </select>
            </div>
        </td>
        <td class="w-1/2"></td>
    </tr>
</table>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Memo:</label>
    <textarea name="comments" rows="3" cols="70"
              class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $cart['comments'] ?? '' }}</textarea>
</div>

@if(count($cart['items']) > 0)
    <div class="flex items-center gap-3">
        <button type="submit" name="Commit"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Process Invoice
        </button>
        <button type="submit" name="CancelOrder"
                class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50">
            Cancel Invoice
        </button>
    </div>
@else
    <button type="submit" name="CancelOrder"
            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50">
        Cancel Invoice
    </button>
@endif

</form>
@endsection