@extends('layouts.app')
@section('title', 'Item Tax Types - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Item Tax Types</h2>
    <p class="mt-2 text-gray-600">Manage item tax types and tax exemptions.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.item-tax-types') }}" class="mb-4">
    @csrf
    <input type="hidden" name="action" value="toggle_show_inactive">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200" style="width:30%">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax exempt</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($types as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $t->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $t->exempt ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.item-tax-types') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $t->id }}">
                            <button type="submit" class="text-sm {{ $t->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $t->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.item-tax-types') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $t->id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.item-tax-types') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this item tax type?');">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="{{ $t->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No item tax types defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_type ? 'Edit Item Tax Type' : 'Add New Item Tax Type' }}
    </h3>
    <form method="POST" action="{{ route('setup.item-tax-types') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_type ? 'update' : 'add' }}">
        @if($edit_type)
            <input type="hidden" name="selected_id" value="{{ $edit_type->id }}">
        @endif

        <div class="max-w-lg mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <input type="text" name="name" value="{{ old('name', $edit_type->name ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="max-w-lg mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Is Fully Tax-exempt</label>
            <select name="exempt" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" id="tax-exempt-select">
                <option value="0" {{ (old('exempt', $edit_type->exempt ?? 0) == 0) ? 'selected' : '' }}>No</option>
                <option value="1" {{ (old('exempt', $edit_type->exempt ?? 0) == 1) ? 'selected' : '' }}>Yes</option>
            </select>
        </div>

        <div id="exemptions-section" class="{{ (old('exempt', $edit_type->exempt ?? 0) == 1) ? 'hidden' : '' }}">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-700">Select which taxes this item tax type is exempt from.</p>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden mb-4">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-100">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tax Name</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Is exempt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tax_types as $tax)
                            <tr class="border-b border-gray-100 hover:bg-white">
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $tax->name }}</td>
                                <td class="px-4 py-2 text-sm text-right font-mono text-gray-700">{{ number_format($tax->rate, 1) }}%</td>
                                <td class="px-4 py-2 text-center">
                                    <input type="checkbox" name="ExemptTax{{ $tax->id }}" value="1" {{ in_array($tax->id, $exempt_tax_ids) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                            </tr>
                        @endforeach
                        @if($tax_types->isEmpty())
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">No tax types available.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_type ? 'Update' : 'Add New' }}
            </button>
            @if($edit_type)
                <a href="{{ route('setup.item-tax-types') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('tax-exempt-select')?.addEventListener('change', function() {
    document.getElementById('exemptions-section').classList.toggle('hidden', this.value === '1');
});
</script>
@endpush