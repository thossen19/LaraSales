@extends('layouts.app')
@section('title', 'Item Adjustments Note')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Item Adjustments Note</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if($addedID)
    @php $itm = \DB::table('stock_moves')->where('trans_type', 17)->where('trans_no', $addedID)->first(); @endphp
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 text-center">
        Items adjustment has been processed
    </div>
    <div class="text-center mb-4">
        <a href="{{ route('inventory.adjust', ['NewAdjustment' => 1]) }}" class="text-indigo-600 hover:text-indigo-900 underline">Enter &Another Adjustment</a>
    </div>
@endif

<form method="POST" action="{{ route('inventory.adjust') }}">
@csrf

@if(!$addedID)
<div class="bg-white shadow rounded-lg p-6 mb-6" style="width:70%">
    <table class="min-w-full">
        <tr>
            <td class="pb-3 pr-4" style="width:25%">
                <label class="text-sm font-medium text-gray-700">Location:</label>
                <select name="StockLocation" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->loc_code }}" {{ $cart['location'] == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="pb-3 pr-4" style="width:25%">
                <label class="text-sm font-medium text-gray-700">Date:</label>
                <input type="date" name="AdjDate" value="{{ $cart['tran_date'] }}" onchange="this.form.submit()" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
            <td class="pb-3" style="width:50%">
                <label class="text-sm font-medium text-gray-700">Reference:</label>
                <input type="text" name="ref" value="{{ old('ref', $cart['reference'] ?: $defaultRef) }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
        </tr>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6" style="width:90%">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Adjustment Items</h3>
    <div id="items_table">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">QOH</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $k = 0; $total = 0; @endphp
                @foreach($cart['line_items'] as $line_no => $stock_item)
                    @php
                        $total += $stock_item['standard_cost'] * $stock_item['quantity'];
                        $qoh = $qohCache[$stock_item['stock_id']] ?? 0;
                        $isLowStock = in_array($stock_item['stock_id'], $lowStockItems);
                    @endphp
                    @if($edit_index !== null && $edit_index == $line_no)
                        <tr class="bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock_item['stock_id'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $stock_item['item_description'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($qoh, 4) }}</td>
                            <td class="px-4 py-3 text-right">
                                <input type="text" name="qty" value="{{ number_format($stock_item['quantity'], 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-700" id="units">{{ $stock_item['units'] }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($stock_item['quantity'] >= 0)
                                    <input type="text" name="std_cost" value="{{ number_format($stock_item['standard_cost'], 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @else
                                    <input type="hidden" name="std_cost" value="0">
                                    <span class="text-sm text-gray-700">{{ number_format($stock_item['standard_cost'], 4) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($stock_item['standard_cost'] * $stock_item['quantity'], 4) }}</td>
                            <td class="px-4 py-3 text-center" colspan="2">
                                <button type="submit" name="UpdateItem" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Update</button>
                                <button type="submit" name="CancelItemChanges" class="px-3 py-1 bg-gray-200 text-gray-800 text-sm rounded hover:bg-gray-300 ml-1">Cancel</button>
                            </td>
                        </tr>
                    @else
                        <tr class="{{ $isLowStock ? 'bg-red-50' : ($k % 2 == 0 ? 'bg-white' : 'bg-gray-50') }}">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $stock_item['stock_id'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $stock_item['item_description'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($qoh, 4) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($stock_item['quantity'], 4) }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $stock_item['units'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($stock_item['standard_cost'], 4) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($stock_item['standard_cost'] * $stock_item['quantity'], 4) }}</td>
                            @if($edit_index === null)
                                <td class="px-4 py-3 text-center">
                                    <button type="submit" name="Edit" value="{{ $line_no }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="submit" name="Delete" value="{{ $line_no }}" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                </td>
                            @else
                                <td class="px-4 py-3 text-center text-sm text-gray-400" colspan="2">-</td>
                            @endif
                        </tr>
                    @endif
                    @php $k++; @endphp
                @endforeach
                @if($edit_index === null)
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3">
                            <select name="stock_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select --</option>
                                @foreach($costableItems as $ci)
                                    <option value="{{ $ci->code }}" {{ request('stock_id') == $ci->code ? 'selected' : '' }}>{{ $ci->code }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            @if(request('stock_id'))
                                @php $selItem = \DB::table('items')->where('code', request('stock_id'))->first(); @endphp
                                <span class="text-sm text-gray-700">{{ $selItem->name ?? '' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">
                            @if(request('stock_id') && $cart['location'])
                                @php $qoh = (float) \DB::table('stock_moves')->where('stock_id', request('stock_id'))->where('loc_code', $cart['location'])->where('tran_date', '<=', $cart['tran_date'])->sum('qty'); @endphp
                                {{ number_format($qoh, 4) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @php $selItem = \DB::table('items')->where('code', request('stock_id'))->first(); @endphp
                            <input type="text" name="qty" value="{{ number_format(0, $selItem ? 2 : 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-700">
                            {{ $selItem->unit_of_measure ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if(request('stock_id'))
                                <input type="text" name="std_cost" value="{{ number_format($selItem->material_cost ?? 0, 4) }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center" colspan="3">
                            <button type="submit" name="AddItem" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Add Item</button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="text-right mt-2 pr-4">
            <span class="text-sm font-medium text-gray-700">Total: </span>
            <span class="text-sm font-bold text-gray-900">{{ number_format($total, 4) }}</span>
        </div>
        @if(count($lowStockItems) > 0)
            <p class="mt-2 text-sm text-red-600">Marked items have insufficient quantities in stock as on day of adjustment.</p>
        @endif
    </div>
</div>

<div class="mt-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-lg">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Memo:</label>
                <textarea name="memo_" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $cart['memo_'] }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 text-center">
    <button type="submit" name="Update" value="1" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition mr-2">Update</button>
    <button type="submit" name="Process" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Process Adjustment</button>
</div>
@endif

</form>
@endsection