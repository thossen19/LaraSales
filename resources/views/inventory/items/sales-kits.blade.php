@extends('layouts.app')
@section('title', 'Sales Kits & Alias Codes')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Sales Kits & Alias Codes</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.items.sales-kits') }}">
@csrf

<div class="text-center mb-4">
    <label class="text-sm font-medium text-gray-700 mr-2">Select a sale kit:</label>
    <select name="item_code" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">-- New kit --</option>
        @foreach($allKits as $k)
            <option value="{{ $k }}" {{ $item_code == $k ? 'selected' : '' }}>{{ $k }}</option>
        @endforeach
    </select>
</div>

@if($selected_kit)
    <div class="bg-white shadow rounded-lg p-6 max-w-lg mb-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                <input type="text" name="description" value="{{ old('description', $props->description ?? '') }}" maxlength="200" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                <select name="category" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0">-- Select --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($props->category_id ?? 0) == $cat->id ? 'selected' : '' }}>{{ $cat->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-center pt-2">
                <button type="submit" name="update_name" value="1" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6" style="width:60%">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($kitComponents as $c)
                    @php
                        $compItem = \DB::table('items')->where('code', $c->stock_id)->first();
                        $compDec = 0;
                        $compUnits = $compItem ? $compItem->unit_of_measure : 'kit';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $c->stock_id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $compItem->name ?? '' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($c->quantity, max($compDec, 0)) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $compUnits }}</td>
                        <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $c->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button></td>
                        <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $c->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No components defined for this kit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <br>
@endif

<div class="bg-white shadow rounded-lg p-6 max-w-lg">
    <input type="hidden" name="selected_id" value="{{ $selected_id }}">

    @if(!$selected_kit)
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alias/kit code:</label>
                <input type="text" name="kit_code" value="{{ old('kit_code', '') }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Component:</label>
                <select name="component" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($componentItems as $ci)
                        <option value="{{ $ci->code }}" {{ old('component', $editComponentCode) == $ci->code ? 'selected' : '' }}>{{ $ci->code }} - {{ $ci->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                <input type="text" name="description" value="{{ old('description', '') }}" maxlength="200" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                <select name="category" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0">-- Select --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category', '') == $cat->id ? 'selected' : '' }}>{{ $cat->description }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity (kits):</label>
                <input type="text" name="quantity" value="{{ old('quantity', $editQuantity) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
    @else
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Component:</label>
                <select name="component" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($componentItems as $ci)
                        <option value="{{ $ci->code }}" {{ old('component', $editComponentCode) == $ci->code ? 'selected' : '' }}>{{ $ci->code }} - {{ $ci->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity ({{ $units }}):</label>
                <input type="text" name="quantity" value="{{ old('quantity', $editQuantity) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
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

</form>
@endsection