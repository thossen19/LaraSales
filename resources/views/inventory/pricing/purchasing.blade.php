@extends('layouts.app')
@section('title', 'Supplier Purchasing Data')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Supplier Purchasing Data</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.pricing.purchasing') }}">
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
    @if($purchData->isEmpty())
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">There is no purchasing data set up for the part selected</div>
    @else
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6" style="width:65%">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier's Unit</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Conversion Factor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier's Description</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($purchData as $i => $pd)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $pd->supp_name }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($pd->price, 4) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $currCode }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $pd->suppliers_uom }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($pd->conversion_factor, 4) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $pd->supplier_description }}</td>
                        <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $pd->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button></td>
                        <td class="px-4 py-3 text-center"><button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $pd->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button></td>
                    </tr>
                    @if(($i + 1) % 12 == 0 && !$loop->last)
                        <tr class="bg-gray-50"><td colspan="8" class="px-4 py-1"></td></tr>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier's Unit</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Conversion Factor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier's Description</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<br>

<div class="bg-white shadow rounded-lg p-6 max-w-lg">
    <input type="hidden" name="selected_id" value="{{ $selected_id }}">

    <div class="space-y-4">
        @if($selected_id > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                <div class="text-sm text-gray-900 font-medium py-2">{{ $suppName }}</div>
                <input type="hidden" name="supplier_id_hidden" value="{{ $editRecord->supplier_id ?? '' }}">
            </div>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                <select name="supplier_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0">-- Select --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ old('supplier_id', '') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price ({{ $currCode }}):</label>
            <input type="text" name="price" value="{{ old('price', $editRecord ? number_format($editRecord->price, 4) : '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Suppliers Unit of Measure:</label>
            <input type="text" name="suppliers_uom" value="{{ old('suppliers_uom', $editRecord->suppliers_uom ?? '') }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Conversion Factor (to our UOM):</label>
            <input type="text" name="conversion_factor" value="{{ old('conversion_factor', $editRecord ? number_format($editRecord->conversion_factor, 4) : 1) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Code or Description:</label>
            <input type="text" name="supplier_description" value="{{ old('supplier_description', $editRecord->supplier_description ?? '') }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
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