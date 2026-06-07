@extends('layouts.app')
@section('title', 'Edit Sales Order - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Edit Sales Order #{{ $order->order_number }}</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('sales.orders.edit', $order) }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-file-invoice mr-2"></i>Order Header</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $cart['customer_id'] == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                    <select name="branch_id" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $cart['branch_id'] == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference <span class="text-red-500">*</span></label>
                    <input type="text" name="ref" value="{{ old('ref', $cart['reference']) }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div>
                @if($customerInfo)
                    <div class="mb-4 p-3 bg-gray-50 rounded-md border border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Customer Currency</span>
                            <span class="text-sm font-medium text-gray-900">USD</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Customer Discount</span>
                            <span class="text-sm font-medium text-gray-900">{{ $customerInfo->discount ?? '0' }}%</span>
                        </div>
                    </div>
                @endif
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment</label>
                    <select name="payment" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($paymentTerms as $pt)
                            <option value="{{ $pt->terms_indicator }}" {{ $cart['payment'] == $pt->terms_indicator ? 'selected' : '' }}>{{ $pt->terms }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price List</label>
                    <select name="sales_type" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($salesTypes as $st)
                            <option value="{{ $st->id }}" {{ $cart['sales_type'] == $st->id ? 'selected' : '' }}>{{ $st->type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date <span class="text-red-500">*</span></label>
                    <input type="date" name="OrderDate" value="{{ $cart['ord_date'] }}" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Required Delivery Date</label>
                    <input type="date" name="delivery_date" value="{{ $cart['delivery_date'] }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deliver from Location</label>
                    <select name="Location" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->loc_code }}" {{ $cart['location'] == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pre-Payment Required</label>
                    <input type="text" name="prep_amount" value="0" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-shopping-cart mr-2"></i>Sales Order Items</h3>
    </div>
    <div class="p-6">
        <div id="items_table">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Discount %</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $k = 0; $total = 0; @endphp
                    @foreach($cart['line_items'] as $line_no => $item)
                        @php
                            $lineTotal = $item['quantity'] * $item['price'] * (1 - $item['discount_percent']);
                            $total += $lineTotal;
                        @endphp
                        @if($edit_index !== null && $edit_index == $line_no)
                            <tr class="bg-indigo-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['stock_id'] }}</td>
                                <td class="px-4 py-3"><input type="text" name="item_description" value="{{ $item['item_description'] }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-right"><input type="text" name="qty" value="{{ number_format($item['quantity'], 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $item['units'] }}</td>
                                <td class="px-4 py-3 text-right"><input type="text" name="price" value="{{ number_format($item['price'], 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-right"><input type="text" name="Disc" value="{{ number_format($item['discount_percent'] * 100, 2) }}" class="w-20 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($lineTotal, 4) }}</td>
                                <td class="px-4 py-3 text-center" colspan="2">
                                    <button type="submit" name="UpdateItem" value="1" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-check mr-1"></i>Update</button>
                                    <button type="submit" name="CancelItemChanges" value="1" class="px-3 py-1.5 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 ml-1"><i class="fas fa-times mr-1"></i>Cancel</button>
                                </td>
                            </tr>
                        @else
                            <tr class="{{ $k % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }} hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['stock_id'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item['item_description'] }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($item['quantity'], 4) }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $item['units'] }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($item['price'], 4) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($item['discount_percent'] * 100, 2) }}%</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($lineTotal, 4) }}</td>
                                @if($edit_index === null)
                                    <td class="px-4 py-3 text-center">
                                        <button type="submit" name="Edit" value="{{ $line_no }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition"><i class="fas fa-edit mr-1"></i>Edit</button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="submit" name="Delete" value="{{ $line_no }}" onclick="return confirm('Are you sure?')" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition"><i class="fas fa-trash mr-1"></i>Delete</button>
                                    </td>
                                @else
                                    <td class="px-4 py-3 text-center text-sm text-gray-400" colspan="2">-</td>
                                @endif
                            </tr>
                        @endif
                        @php $k++; @endphp
                    @endforeach
                    @if($edit_index === null)
                        <tr class="bg-gray-50/80">
                            <td class="px-4 py-3">
                                <div class="flex">
                                    <input type="text" name="stock_id" value="{{ $cart['stock_id'] }}" placeholder="Item Code" class="flex-1 border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" onclick="openItemSearch()" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($cart['stock_id'])
                                    @php $selItem = \DB::table('items')->where('code', $cart['stock_id'])->first(); @endphp
                                    <input type="text" name="item_description" value="{{ $cart['item_description'] ?: ($selItem->name ?? '') }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <input type="text" name="qty" value="{{ number_format($cart['qty'] ?: 1, 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-700">
                                @if($cart['stock_id']){{ $selItem->unit_of_measure ?? '' }}@endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($cart['stock_id'])
                                    <input type="text" name="price" value="{{ number_format($cart['price'] ?: ($selItem->cost_price ?? 0), 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <input type="text" name="Disc" value="{{ number_format($cart['Disc'] ?? 0, 2) }}" class="w-20 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-right">&nbsp;</td>
                            <td class="px-4 py-3 text-center" colspan="2">
                                <button type="submit" name="AddItem" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-plus mr-1"></i>Add Item</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>

            <div class="mt-4 bg-gray-50 rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-end gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Shipping Charge:</span>
                        <input type="text" name="freight_cost" value="{{ number_format($cart['freight_cost'] ?? 0, 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="submit" name="update" value="1" class="px-4 py-1.5 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300"><i class="fas fa-sync-alt mr-1"></i>Update</button>
                </div>
                <div class="flex items-center justify-end gap-6 mt-2">
                    @php $subtotal = $total + ($cart['freight_cost'] ?? 0); @endphp
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Sub-total:</span>
                        <span class="ml-2 text-sm font-semibold text-gray-900">{{ number_format($subtotal, 4) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Amount Total:</span>
                        <span class="ml-2 text-base font-bold text-indigo-700">{{ number_format($subtotal, 4) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-truck mr-2"></i>Order Delivery Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deliver To <span class="text-red-500">*</span></label>
                    <input type="text" name="deliver_to" value="{{ $cart['deliver_to'] }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <textarea name="delivery_address" rows="3" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $cart['delivery_address'] }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone Number</label>
                    <input type="text" name="phone" value="{{ $cart['phone'] }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Reference</label>
                    <input type="text" name="cust_ref" value="{{ $cart['cust_ref'] }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                    <textarea name="Comments" rows="3" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $cart['comments'] }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Company</label>
                    <select name="ship_via" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Select --</option>
                        @foreach($shippers as $sh)
                            <option value="{{ $sh->shipper_id }}" {{ $cart['ship_via'] == $sh->shipper_id ? 'selected' : '' }}>{{ $sh->shipper_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="CancelOrder" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Cancel</button>
    <button type="submit" name="UpdateOrder" value="1" class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-md hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm"><i class="fas fa-save mr-2"></i>Update Order</button>
</div>
</form>
@include('components.item-search-modal')
@endsection