@extends('layouts.app')
@section('title', 'Transaction References - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Transaction References</h2>
    <p class="mt-2 text-gray-600">Configure reference numbering patterns for each transaction type.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.transaction-references') }}" class="mb-4">
    @csrf
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
    <noscript><button type="submit" class="ml-2 px-3 py-1 bg-indigo-600 text-white text-sm rounded">Refresh</button></noscript>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prefix</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pattern</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Default</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($reflines as $refline)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $systypes_array[$refline->trans_type] ?? 'Unknown' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $refline->prefix }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $refline->pattern }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $refline->default ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $refline->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.transaction-references') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $refline->id }}">
                            <button type="submit" class="text-sm {{ $refline->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $refline->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.transaction-references') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $refline->id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm mr-2">Edit</button>
                        </form>
                        @if(!$refline->default)
                            <form method="POST" action="{{ route('setup.transaction-references') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this reference line?');">
                                @csrf
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="selected_id" value="{{ $refline->id }}">
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No reference lines defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_refline ? 'Edit Reference Line' : 'Add New Reference Line' }}
    </h3>
    <form method="POST" action="{{ route('setup.transaction-references') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_refline ? 'update' : 'add' }}">
        @if($edit_refline)
            <input type="hidden" name="selected_id" value="{{ $edit_refline->id }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                @if($edit_refline)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <p class="text-sm text-gray-900 py-2 px-3 bg-gray-50 rounded-md">{{ $systypes_array[$edit_refline->trans_type] ?? 'Unknown' }}</p>
                        <input type="hidden" name="trans_type" value="{{ $edit_refline->trans_type }}">
                    </div>
                @else
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <select name="trans_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('trans_type') border-red-500 @enderror">
                            <option value="">-- Select --</option>
                            @foreach($systypes_array as $type_id => $type_name)
                                <option value="{{ $type_id }}" {{ old('trans_type', $edit_refline->trans_type ?? '') == $type_id ? 'selected' : '' }}>{{ $type_name }}</option>
                            @endforeach
                        </select>
                        @error('trans_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference Pattern</label>
                    <div class="flex items-center">
                        @if($edit_refline)
                            <span class="inline-flex items-center px-3 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md text-sm text-gray-700 font-mono">{{ $edit_refline->prefix }}</span>
                            <input type="hidden" name="prefix" value="{{ $edit_refline->prefix }}">
                        @else
                            <input type="text" name="prefix" placeholder="Prefix" value="{{ old('prefix', $edit_refline->prefix ?? '') }}" maxlength="30" class="w-24 border border-gray-300 rounded-l-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('prefix') border-red-500 @enderror">
                        @endif
                        <input type="text" name="pattern" placeholder="Pattern (e.g. {001})" value="{{ old('pattern', $edit_refline->pattern ?? '') }}" maxlength="60" class="flex-1 border border-gray-300 {{ $edit_refline ? '' : 'rounded-r-md' }} px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('pattern') border-red-500 @enderror">
                    </div>
                    @error('prefix') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('pattern') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <div class="mb-4">
                    @if($edit_refline && $edit_refline->default)
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default for This Type</label>
                        <p class="text-sm text-gray-900 py-2 px-3 bg-gray-50 rounded-md">Yes</p>
                        <input type="hidden" name="default" value="1">
                    @else
                        <label class="flex items-center">
                            <input type="checkbox" name="default" value="1" {{ old('default', $edit_refline->default ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Set as Default for This Type</span>
                        </label>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Memo</label>
                    <input type="text" name="description" value="{{ old('description', $edit_refline->description ?? '') }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_refline ? 'Update' : 'Add New' }}
            </button>
            @if($edit_refline)
                <a href="{{ route('setup.transaction-references') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h4 class="text-sm font-medium text-blue-800 mb-2">Placeholder Reference</h4>
    <p class="text-xs text-blue-700 leading-relaxed">
        Use <code class="bg-blue-100 px-1 rounded">{001}</code> for auto-incrementing number (padded to given digit count).
        Available date placeholders: <code class="bg-blue-100 px-1 rounded">{MM}</code> (month),
        <code class="bg-blue-100 px-1 rounded">{YY}</code> (2-digit year),
        <code class="bg-blue-100 px-1 rounded">{YYYY}</code> (4-digit year),
        <code class="bg-blue-100 px-1 rounded">{FF}</code> (2-digit fiscal year),
        <code class="bg-blue-100 px-1 rounded">{FFFF}</code> (4-digit fiscal year).
        User placeholder: <code class="bg-blue-100 px-1 rounded">{UU}</code>.
        POS placeholder: <code class="bg-blue-100 px-1 rounded">{P}</code>.
        Example: <code class="bg-blue-100 px-1 rounded">INV-{YYYY}-{MM}-{001}</code> produces <code class="bg-blue-100 px-1 rounded">INV-2026-05-001</code>.
    </p>
</div>
@endsection