@extends('layouts.app')
@section('title', 'Credit Status Setup - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Credit Status Setup</h2>
    <p class="mt-2 text-gray-600">Configure credit status levels for customers.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('sales.setup.credit-status') }}">
@csrf
<input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Dissallow Invoices</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($statuses as $cs)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $cs->status_name }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        @if($cs->dissallow_invoices)
                            <span class="font-semibold text-red-600">NO INVOICING</span>
                        @else
                            <span class="text-green-600">Invoice OK</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('sales.setup.credit-status', ['toggle_inactive' => $cs->id, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $cs->status === 'inactive' ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $cs->status === 'inactive' ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $cs->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $cs->id }}';return confirm('Are you sure you want to delete this credit status?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No credit status defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mb-6">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id > 0 ? 'Edit Credit Status' : 'Add New Credit Status' }}
    </h3>

    <div class="space-y-4 max-w-lg">
        @if($selected_id > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID:</label>
                <input type="text" value="{{ $edit_status->id ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-500" readonly>
            </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
            <input type="text" name="reason_description" value="{{ old('reason_description', $edit_reason_description) }}" maxlength="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dissallow invoicing ?</label>
            <div class="flex items-center space-x-4">
                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                    <input type="radio" name="DisallowInvoices" value="1" {{ old('DisallowInvoices', $edit_disallow_invoices) ? 'checked' : '' }} class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2">Yes</span>
                </label>
                <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                    <input type="radio" name="DisallowInvoices" value="0" {{ !old('DisallowInvoices', $edit_disallow_invoices) ? 'checked' : '' }} class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2">No</span>
                </label>
            </div>
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
