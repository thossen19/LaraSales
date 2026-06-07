@extends('layouts.app')
@section('title', 'Tax Types - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Tax Types</h2>
    <p class="mt-2 text-gray-600">Manage tax types and default tax rates.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.taxes') }}" class="mb-4">
    @csrf
    <input type="hidden" name="action" value="toggle_show_inactive">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <p class="text-sm text-blue-700">To avoid problems with manual journal entry all tax types should have unique Sales/Purchasing GL accounts.</p>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Default Rate (%)</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sales GL Account</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchasing GL Account</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tax_types as $tax)
                <tr class="hover:bg-gray-50 {{ $tax->inactive ? 'text-gray-400' : '' }}">
                    <td class="px-4 py-3 text-sm">{{ $tax->name }}</td>
                    <td class="px-4 py-3 text-sm text-right font-mono">{{ number_format($tax->rate, 1) }}%</td>
                    <td class="px-4 py-3 text-sm">{{ $tax->salesGlAccount ? $tax->salesGlAccount->code . ' ' . $tax->salesGlAccount->name : '' }}</td>
                    <td class="px-4 py-3 text-sm">{{ $tax->purchasingGlAccount ? $tax->purchasingGlAccount->code . ' ' . $tax->purchasingGlAccount->name : '' }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.taxes') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $tax->id }}">
                            <button type="submit" class="text-sm {{ $tax->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $tax->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.taxes') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $tax->id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.taxes') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this tax type?');">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="{{ $tax->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No tax types defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_tax ? 'Edit Tax Type' : 'Add New Tax Type' }}
    </h3>
    <form method="POST" action="{{ route('setup.taxes') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_tax ? 'update' : 'add' }}">
        @if($edit_tax)
            <input type="hidden" name="selected_id" value="{{ $edit_tax->id }}">
        @endif

        <div class="space-y-4 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="name" value="{{ old('name', $edit_tax->name ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Rate (%)</label>
                <div class="flex items-center">
                    <input type="text" name="rate" value="{{ old('rate', $edit_tax->rate ?? '0.0') }}" class="w-32 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('rate') border-red-500 @enderror">
                    <span class="ml-2 text-sm text-gray-500">%</span>
                </div>
                @error('rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sales GL Account</label>
                <select name="sales_gl_code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($gl_accounts as $account)
                        <option value="{{ $account->code }}" {{ old('sales_gl_code', $edit_tax->sales_gl_code ?? '') == $account->code ? 'selected' : '' }}>
                            {{ $account->code }} {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchasing GL Account</label>
                <select name="purchasing_gl_code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($gl_accounts as $account)
                        <option value="{{ $account->code }}" {{ old('purchasing_gl_code', $edit_tax->purchasing_gl_code ?? '') == $account->code ? 'selected' : '' }}>
                            {{ $account->code }} {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_tax ? 'Update' : 'Add New' }}
            </button>
            @if($edit_tax)
                <a href="{{ route('setup.taxes') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection