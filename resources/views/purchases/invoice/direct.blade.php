@extends('layouts.app')
@section('title', 'Direct Purchase Invoice Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Direct Purchase Invoice Entry</h2>
    <p class="mt-2 text-gray-600">Create a direct supplier invoice for items received.</p>
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

<form method="POST" action="{{ route('purchases.invoice.direct') }}" id="invoice-form">
@csrf

<!-- Header Section -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                <select name="supplier_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select a supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $cart['supplier_id'] == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date:</label>
                <input type="date" name="invoice_date" value="{{ $cart['invoice_date'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference:</label>
                <input type="text" name="reference" value="{{ $cart['reference'] }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            @if($supplier && $supplier->curr_code && $supplier->curr_code != 'USD')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Currency:</label>
                    <p class="text-sm text-gray-900 py-2">{{ $supplier->curr_code }}</p>
                </div>
            @endif
        </div>
        <!-- Middle Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date:</label>
                <input type="date" name="due_date" value="{{ $cart['due_date'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Reference:</label>
                <input type="text" name="supp_reference" value="{{ $cart['supp_reference'] }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension:</label>
                <select name="dimension_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="0">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $cart['dimension_id'] == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 2:</label>
                <select name="dimension2_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="0">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $cart['dimension2_id'] == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Receive Into:</label>
                <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select location --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->loc_code }}" {{ $cart['location'] == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Right Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deliver to:</label>
                <textarea name="delivery_address" rows="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ $cart['delivery_address'] }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Invoice Items</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="items-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Line Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" style="width:100px">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $total = 0; @endphp
                @forelse($cart['items'] as $line_no => $line_item)
                    @php
                        $line_total = $line_item['quantity'] * $line_item['price'];
                        $total += $line_total;
                    @endphp
                    @if($cart['edit_line'] === $line_no)
                        <tr class="bg-yellow-50">
                            <td class="px-4 py-2">
                                <input type="hidden" name="stock_id" value="{{ $line_item['stock_id'] }}">
                                <span class="text-sm font-medium text-gray-900">{{ $line_item['stock_id'] }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="item_description" value="{{ $line_item['item_description'] }}" maxlength="150" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="qty" value="{{ $line_item['quantity'] }}" min="1" step="1" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            </td>
                            <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $line_item['unit'] }}</td>
                            <td class="px-4 py-2">
                                <input type="number" name="price" value="{{ $line_item['price'] }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-2 text-right text-sm text-gray-900 font-medium">{{ number_format($line_total, 2) }}</td>
                            <td class="px-4 py-2 text-center">
                                <button type="submit" name="UpdateLine" value="1" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">Update</button>
                                <button type="submit" name="CancelUpdate" value="1" class="px-3 py-1 text-xs bg-gray-400 text-white rounded hover:bg-gray-500 ml-1">Cancel</button>
                            </td>
                        </tr>
                    @else
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $line_item['stock_id'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $line_item['item_description'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $line_item['quantity'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $line_item['unit'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($line_item['price'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ number_format($line_total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <button type="submit" name="Edit{{ $line_no }}" value="1" class="text-indigo-600 hover:text-indigo-900 text-sm mr-2">Edit</button>
                                <button type="submit" name="Delete{{ $line_no }}" value="1" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Delete this line item?')">Delete</button>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No items added yet. Select an item below and click Add Item.</td>
                    </tr>
                @endforelse

                <!-- Add Item Row -->
                @if($cart['edit_line'] < 0)
                <tr class="bg-gray-50">
                    <td class="px-4 py-2">
                        <select name="stock_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Select item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->code }}" {{ ($cart['stock_id'] ?? '') == $item->code ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-2">
                        @php
                            $desc = $cart['item_description'] ?? '';
                            if ((empty($cart['stock_id']) || empty($cart['item_description'])) && !empty($selected_item_info)) {
                                $desc = $selected_item_info->name;
                            }
                        @endphp
                        <input type="text" name="item_description" value="{{ $desc }}" maxlength="150" placeholder="Item description" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-2">
                        <input type="number" name="qty" value="{{ $cart['qty'] ?? 1 }}" min="1" step="1" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-2 text-center text-sm text-gray-700">
                        {{ $selected_item_info->unit_of_measure ?? 'each' }}
                    </td>
                    <td class="px-4 py-2">
                        <input type="number" name="price" value="{{ $cart['price'] ?? '0.00' }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-2 text-right text-sm text-gray-900 font-medium">
                        @php $add_total = ($cart['qty'] ?? 1) * ($cart['price'] ?? 0); @endphp
                        {{ number_format($add_total, 2) }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button type="submit" name="EnterLine" value="1" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">Add Item</button>
                    </td>
                </tr>
                @endif

                <!-- Totals -->
                <tr class="bg-gray-100">
                    <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Sub-total</td>
                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ number_format($total, 2) }}</td>
                </tr>
                @php
                    $tax_total = 0;
                @endphp
                <tr class="bg-gray-100">
                    <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Amount Total</td>
                    <td colspan="3" class="px-4 py-3 text-right text-sm font-bold text-gray-900 text-lg">{{ number_format($total + $tax_total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment:</label>
            <select name="cash_account_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">Delayed</option>
                @foreach($cashAccounts as $acct)
                    <option value="{{ $acct->id }}" {{ ($cart['cash_account_id'] ?? '') == $acct->id ? 'selected' : '' }}>{{ $acct->bank_account_name }} ({{ $acct->bank_curr_code ?? 'USD' }})</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Select a cash account for immediate payment, or leave as Delayed for later payment.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Memo:</label>
            <textarea name="comments" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ $cart['comments'] }}</textarea>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-center space-x-4">
    @if(!empty($cart['items']))
        <button type="submit" name="Commit" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Process Invoice</button>
    @endif
    <button type="submit" name="CancelOrder" value="1" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition" onclick="return confirm('Cancel this direct purchase invoice entry?')">Cancel Invoice</button>
</div>

</form>
@endsection
