@extends('layouts.app')
@section('title', 'Tax Groups - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Tax Groups</h2>
    <p class="mt-2 text-gray-600">Manage tax groups and assign tax types to each group.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

@if($tax_types->isEmpty())
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">There are no tax types defined. Define tax types before defining tax groups.</div>
@endif

<form method="POST" action="{{ route('setup.tax-groups') }}" class="mb-4">
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
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($groups as $group)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $group->name }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.tax-groups') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $group->id }}">
                            <button type="submit" class="text-sm {{ $group->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $group->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.tax-groups') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $group->id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.tax-groups') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this tax group?');">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="{{ $group->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No tax groups defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_group ? 'Edit Tax Group' : 'Add New Tax Group' }}
    </h3>
    <form method="POST" action="{{ route('setup.tax-groups') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_group ? 'update' : 'add' }}">
        @if($edit_group)
            <input type="hidden" name="selected_id" value="{{ $edit_group->id }}">
        @endif

        <div class="max-w-lg mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <input type="text" name="name" value="{{ old('name', $edit_group->name ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-gray-600 mb-3">Select the taxes that are included in this group.</p>
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tax</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Included</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Shipping Tax</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tax_types as $tax)
                        <tr class="border-b border-gray-100 hover:bg-white">
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $tax->name }} ({{ number_format($tax->rate, 1) }}%)</td>
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox" name="tax_type_id_{{ $tax->id }}" value="1" {{ in_array($tax->id, $group_tax_ids ?? []) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 tax-type-checkbox" data-tax-id="{{ $tax->id }}">
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox" name="tax_shipping_{{ $tax->id }}" value="1" {{ in_array($tax->id, $group_shipping_ids ?? []) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shipping-checkbox" data-tax-id="{{ $tax->id }}">
                            </td>
                        </tr>
                    @endforeach
                    @if($tax_types->isEmpty())
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">No tax types available. Please define tax types first.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150" {{ $tax_types->isEmpty() ? 'disabled' : '' }}>
                {{ $edit_group ? 'Update' : 'Add New' }}
            </button>
            @if($edit_group)
                <a href="{{ route('setup.tax-groups') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection