@extends('layouts.app')
@section('title', 'Direct Delivery Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Direct Delivery Entry</h2>
    <p class="mt-1 text-sm text-gray-500">Process direct deliveries without creating sales orders first.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if($addedID)
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 text-center">
        Delivery # {{ $addedID }} has been entered.
    </div>
    <div class="text-center mb-4">
        <a href="{{ route('sales.delivery.direct', ['NewDelivery' => 'Yes']) }}" class="text-indigo-600 hover:text-indigo-900 underline">Enter a New Delivery</a>
    </div>
@else

<form method="POST" action="{{ route('sales.delivery.direct') }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-truck mr-2"></i>Delivery Header</h3>
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
                    <input type="text" name="ref" value="{{ old('ref', $cart['reference'] ?: $defaultRef) }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Date <span class="text-red-500">*</span></label>
                    <input type="date" name="OrderDate" value="{{ $cart['ord_date'] }}" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
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
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-boxes mr-2"></i>Delivery Items</h3>
    </div>
    <div class="p-6">
        <div id="items_table">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $k = 0; @endphp
                    @foreach($cart['line_items'] as $line_no => $item)
                        @if($edit_index !== null && $edit_index == $line_no)
                            <tr class="bg-indigo-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['stock_id'] }}</td>
                                <td class="px-4 py-3"><input type="text" name="item_description" value="{{ $item['item_description'] }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-right"><input type="text" name="qty" value="{{ number_format($item['quantity'], 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-1.5 text-right text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $item['units'] }}</td>
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
                            <td class="px-4 py-3 text-center" colspan="2">
                                <button type="submit" name="AddItem" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-plus mr-1"></i>Add Item</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-map-marker-alt mr-2"></i>Delivery Details</h3>
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
    <button type="submit" name="CancelOrder" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Cancel Delivery</button>
    <button type="submit" name="ProcessOrder" value="1" class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-md hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm"><i class="fas fa-truck mr-2"></i>Process Delivery</button>
</div>
</form>
@endif
@include('components.item-search-modal')
@endsection
