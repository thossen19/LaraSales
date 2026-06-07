@extends('layouts.app')
@section('title', 'Chart of Accounts - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Chart of Accounts</h2>
    <p class="mt-2 text-gray-600">Manage general ledger accounts.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.gl-accounts') }}">
@csrf

<div class="bg-white shadow rounded-lg p-4 mb-6">
    <div class="flex items-center gap-4 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <select name="selected_account" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">-- New account --</option>
                @foreach($accounts as $a)
                    <option value="{{ $a->code }}" {{ $selected_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center text-sm text-gray-700 cursor-pointer whitespace-nowrap">
            <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ml-2">Show inactive:</span>
        </label>
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <div class="space-y-4 max-w-lg">
        @if($selected_account)
            <input type="hidden" name="account_code" value="{{ $edit_code }}">
            <input type="hidden" name="selected_account" value="{{ $selected_account }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Code:</label>
                <p class="py-2 text-sm text-gray-800 font-medium">{{ $edit_code }}</p>
            </div>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Code:</label>
                <input type="text" name="account_code" value="" maxlength="15" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Code 2:</label>
            <input type="text" name="account_code2" value="{{ old('account_code2', $edit_code2) }}" maxlength="15" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Name:</label>
            <input type="text" name="account_name" value="{{ old('account_name', $edit_name) }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Group:</label>
            <select name="account_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">-- Select --</option>
                @foreach($account_groups as $val => $label)
                    <option value="{{ $val }}" {{ $edit_type == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Tags:</label>
            <select name="account_tags[]" multiple size="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @foreach($tags as $t)
                    <option value="{{ $t->id }}" {{ in_array($t->id, $selected_tags) ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" name="inactive" value="1" {{ $edit_inactive ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Account status: {{ $edit_inactive ? 'Inactive' : 'Active' }}</span>
            </label>
        </div>
    </div>

    <div class="pt-4 mt-4 border-t border-gray-200">
        @if($selected_account)
            <button type="submit" name="update" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update Account</button>
            <button type="submit" name="delete" value="1" class="px-6 py-2 ml-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition" onclick="return confirm('Are you sure?')">Delete account</button>
        @else
            <button type="submit" name="add" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add Account</button>
        @endif
    </div>
</div>

</form>
@endsection