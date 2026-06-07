@extends('layouts.app')
@section('title', 'Shipping Company - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Shipping Company</h2>
    <p class="mt-2 text-gray-600">Manage shipping companies.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.shipping-company') }}" class="mb-4">
    @csrf
    <input type="hidden" name="action" value="toggle_show_inactive">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone Number</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secondary Phone</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($shippers as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $s->shipper_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $s->contact }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $s->phone }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $s->phone2 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $s->address }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.shipping-company') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $s->shipper_id }}">
                            <button type="submit" class="text-sm {{ $s->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $s->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.shipping-company') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $s->shipper_id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.shipping-company') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipping company?');">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="{{ $s->shipper_id }}">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No shipping companies defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_shipper ? 'Edit Shipping Company' : 'Add New Shipping Company' }}
    </h3>
    <form method="POST" action="{{ route('setup.shipping-company') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_shipper ? 'update' : 'add' }}">
        @if($edit_shipper)
            <input type="hidden" name="selected_id" value="{{ $edit_shipper->shipper_id }}">
        @endif

        <div class="space-y-4 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name:</label>
                <input type="text" name="shipper_name" value="{{ old('shipper_name', $edit_shipper->shipper_name ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('shipper_name') border-red-500 @enderror">
                @error('shipper_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person:</label>
                <input type="text" name="contact" value="{{ old('contact', $edit_shipper->contact ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number:</label>
                <input type="text" name="phone" value="{{ old('phone', $edit_shipper->phone ?? '') }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone Number:</label>
                <input type="text" name="phone2" value="{{ old('phone2', $edit_shipper->phone2 ?? '') }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
                <input type="text" name="address" value="{{ old('address', $edit_shipper->address ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_shipper ? 'Update' : 'Add New' }}
            </button>
            @if($edit_shipper)
                <a href="{{ route('setup.shipping-company') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection