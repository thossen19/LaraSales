@extends('layouts.app')
@section('title', 'Sales Types - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Sales Types</h2>
    <p class="mt-2 text-gray-600">Manage sales types for classification and pricing.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('sales.setup.types') }}">
@csrf
<input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type Name</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Factor</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tax Incl</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($types as $type)
                <tr class="hover:bg-gray-50 {{ $type->id == $base_sales_type_id ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-3 text-sm {{ $type->id == $base_sales_type_id ? 'font-semibold text-gray-900' : 'text-gray-900' }}">{{ $type->type_name }}</td>
                    <td class="px-4 py-3 text-sm text-center {{ $type->id == $base_sales_type_id ? 'text-gray-500 italic' : 'text-gray-700' }}">
                        @if($type->id == $base_sales_type_id)
                            <em>Base</em>
                        @else
                            {{ number_format($type->factor, 4) }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $type->tax_included ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('sales.setup.types', ['toggle_inactive' => $type->id, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $type->status === 'inactive' ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $type->status === 'inactive' ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $type->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $type->id }}';return confirm('Are you sure you want to delete this sales type?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No sales types defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mb-4">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</div>

<p class="text-sm text-yellow-700 mb-6 bg-yellow-50 px-3 py-2 rounded">
    <em>Marked sales type is the company base pricelist for prices calculations.</em>
</p>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id > 0 ? 'Edit Sales Type' : 'Add New Sales Type' }}
    </h3>

    <div class="space-y-4 max-w-lg">
        @if($selected_id > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID:</label>
                <input type="text" value="{{ $edit_type->id ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-500" readonly>
            </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sales Type Name:</label>
            <input type="text" name="type_name" value="{{ old('type_name', $edit_type_name) }}" maxlength="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Calculation factor:</label>
            <input type="text" name="factor" value="{{ old('factor', $edit_factor) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="tax_included" value="1" {{ old('tax_included', $edit_tax_included) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2">Tax included</span>
            </label>
        </div>
    </div>

    <div class="pt-4 mt-4 border-t border-gray-200">
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
