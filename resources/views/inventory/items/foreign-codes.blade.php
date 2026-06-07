@extends('layouts.app')
@section('title', 'Foreign Item Codes')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Foreign Item Codes</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.items.foreign-codes') }}">
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
<div class="bg-white shadow rounded-lg overflow-hidden mb-6" style="width:60%">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">EAN/UPC Code</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($codes as $i => $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $c->item_code }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($c->quantity, 4) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $units }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $c->description }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ \DB::table('stock_category')->where('id', $c->category_id)->value('description') ?? '' }}</td>
                    <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $c->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button></td>
                    <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $c->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button></td>
                </tr>
                @if(($i + 1) % 12 == 0 && !$loop->last)
                    <tr class="bg-gray-50"><td colspan="7" class="px-4 py-1"></td></tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">EAN/UPC Code</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No foreign item codes defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<br>

<div class="bg-white shadow rounded-lg p-6 max-w-lg">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id > 0 ? 'Edit Foreign Item Code' : 'Add New Foreign Item Code' }}
    </h3>

    <input type="hidden" name="selected_id" value="{{ $selected_id }}">

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">UPC/EAN code:</label>
            <input type="text" name="item_code" value="{{ old('item_code', $edit_item_code ?: request('item_code', '')) }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity ({{ $units }}):</label>
            <input type="text" name="quantity" value="{{ old('quantity', $edit_quantity ?: (request('quantity', 1))) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
            <input type="text" name="description" value="{{ old('description', $edit_description ?: request('description', '')) }}" maxlength="200" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
            <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="0">-- Select --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($selected_id > 0 ? $edit_category_id : (request('category_id', $dflt_cat))) == $cat->id ? 'selected' : '' }}>{{ $cat->description }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="pt-4 mt-4 border-t border-gray-200 text-center">
        @if($selected_id > 0)
            <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
            <button type="submit" name="Mode" value="RESET" class="px-6 py-2 ml-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
        @else
            <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
        @endif
    </div>
</div>
@endif

</form>
@endsection