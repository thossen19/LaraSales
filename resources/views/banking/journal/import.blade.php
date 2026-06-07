@extends('layouts.app')
@section('title', 'Import Multiple Journal Entries - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Import Multiple Journal Entries / Deposits / Payments</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.journal.import') }}" enctype="multipart/form-data">
@csrf

<div class="bg-white shadow rounded-lg p-6">
    <table class="w-full">
        <tr>
            <td colspan="2" class="text-lg font-medium text-gray-800 pb-4">Import Settings</td>
        </tr>
        <tr>
            <td class="py-2 pr-4 text-sm font-medium text-gray-700 whitespace-nowrap w-40">Import Type:</td>
            <td class="py-2">
                <select name="type" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($import_types as $val => $label)
                        <option value="{{ $val }}" {{ $type == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @if($type != '0')
        <tr>
            <td class="py-2 pr-4 text-sm font-medium text-gray-700 whitespace-nowrap">{{ $type == '2' ? 'From:' : 'To:' }}</td>
            <td class="py-2">
                <select name="bank_account" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($bank_accounts as $ba)
                        <option value="{{ $ba->id }}" {{ $bank_account == $ba->id ? 'selected' : '' }}>{{ $ba->bank_account_name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @endif
        <tr>
            <td class="py-2 pr-4 text-sm font-medium text-gray-700 whitespace-nowrap">Field Separator:</td>
            <td class="py-2">
                <input type="text" name="sep" value="{{ $sep }}" maxlength="1" class="border border-gray-300 rounded-md px-3 py-2 w-16 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-4 text-sm font-medium text-gray-700 whitespace-nowrap">Import File:</td>
            <td class="py-2">
                <input type="file" id="imp" name="imp" class="text-sm">
            </td>
        </tr>
    </table>
</div>

<div class="mt-6 text-center">
    <button type="submit" name="import" value="1" class="px-8 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Perform Import</button>
</div>

</form>
@endsection