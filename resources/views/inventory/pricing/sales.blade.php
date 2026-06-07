@extends('layouts.app')
@section('title', 'Inventory Item Sales prices')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Inventory Item Sales prices</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.pricing.sales') }}">
@csrf

<div class="text-center mb-4">
    <label class="text-sm font-medium text-gray-700 mr-2">Item:</label>
    <select name="stock_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">-- Select --</option>
        @foreach($items as $it)
            <option value="{{ $it->code }}" {{ $stock_id == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
        @endforeach
    </select>
    <hr class="mt-4">
</div>

@if($stock_id)
<div id="price_table">
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6" style="width:30%">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sales Type</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($prices as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $p->curr_abrev }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $p->sales_type }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($p->price, 4) }}</td>
                        <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $p->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button></td>
                        <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $p->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">There are no prices set up for this part.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php
    $item = \DB::table('items')->where('code', $stock_id)->first();
    $units = $item ? $item->unit_of_measure : '';
    $addPct = \DB::table('settings')->where('key', 'add_pct')->value('value');
    $calculated = ($addPct !== null && $addPct != -1) && $prices->isEmpty();
@endphp

<div id="price_details">
    <div class="bg-white shadow rounded-lg p-6 max-w-lg">
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency:</label>
                <select name="curr_abrev" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($currencies as $cur)
                        <option value="{{ $cur->curr_abrev }}" {{ ($editPrice ? $editPrice->curr_abrev : request('curr_abrev', $curr_abrev)) == $cur->curr_abrev ? 'selected' : '' }}>{{ $cur->curr_abrev }} - {{ $cur->currency }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sales Type:</label>
                <select name="sales_type_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($salesTypes as $st)
                        <option value="{{ $st->id }}" {{ ($editPrice ? $editPrice->sales_type_id : request('sales_type_id', 0)) == $st->id ? 'selected' : '' }}>{{ $st->type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (per {{ $units }}):</label>
                <input type="text" name="price" value="{{ $editPrice ? number_format($editPrice->price, 4) : request('price', $defaultPrice > 0 ? number_format($defaultPrice, 4) : '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        @if($calculated && !$editPrice)
            <div class="mt-2 text-sm text-gray-500 italic">The price is calculated.</div>
        @endif

        <div class="pt-4 mt-4 border-t border-gray-200 text-center">
            @if($selected_id > 0)
                <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
                <button type="submit" name="Mode" value="RESET" class="px-6 py-2 ml-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
            @else
                <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
            @endif
        </div>
    </div>
</div>
@endif

</form>
@endsection