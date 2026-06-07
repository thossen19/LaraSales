@extends('layouts.app')
@section('title', 'Bank Account Transfer Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Bank Account Transfer Entry</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.transfers') }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="w-full">
        <tr>
            <td class="p-4 align-top w-1/2">
                <table class="w-full">
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">From Account:</td>
                        <td class="py-2">
                            <select name="FromBankAccount" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select --</option>
                                @foreach($bank_accounts as $ba)
                                    <option value="{{ $ba->id }}" {{ request('FromBankAccount') == $ba->id ? 'selected' : '' }}>{{ $ba->bank_account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @php
                        $fromSelected = request('FromBankAccount') ? \DB::table('bank_accounts')->where('id', request('FromBankAccount'))->first() : null;
                    @endphp
                    @if($fromSelected)
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Bank Balance:</td>
                        <td class="py-2 text-sm text-gray-700">0.00</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">To Account:</td>
                        <td class="py-2">
                            <select name="ToBankAccount" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select --</option>
                                @foreach($bank_accounts as $ba)
                                    <option value="{{ $ba->id }}" {{ request('ToBankAccount') == $ba->id ? 'selected' : '' }}>{{ $ba->bank_account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Transfer Date:</td>
                        <td class="py-2">
                            <input type="date" name="DatePaid" value="{{ request('DatePaid', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
                        <td class="py-2">
                            <input type="text" name="ref" value="{{ request('ref') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                    </tr>
                    @if($use_dimension > 0)
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension:</td>
                        <td class="py-2">
                            <select name="dimension_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- None --</option>
                                @foreach($dimensions as $d)
                                    <option value="{{ $d->id }}" {{ request('dimension_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @else
                    <input type="hidden" name="dimension_id" value="">
                    @endif
                </table>
            </td>
            <td class="p-4 align-top w-1/2">
                <table class="w-full">
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">
                            Amount:
                            @if($multiCurrency)
                                <span class="text-gray-500 font-normal">({{ $fromCurrency }})</span>
                            @endif
                        </td>
                        <td class="py-2">
                            <input type="text" name="amount" value="{{ request('amount') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">
                            Bank Charge:
                            @if($multiCurrency)
                                <span class="text-gray-500 font-normal">({{ $fromCurrency }})</span>
                            @endif
                        </td>
                        <td class="py-2">
                            <input type="text" name="charge" value="{{ request('charge', '0') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                    </tr>
                    @if($multiCurrency)
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">
                            Incoming Amount:
                            <span class="text-gray-500 font-normal">({{ $toCurrency }})</span>
                        </td>
                        <td class="py-2">
                            <input type="text" name="target_amount" value="{{ request('target_amount') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                    </tr>
                    @endif
                    @if($use_dimension > 1)
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 2:</td>
                        <td class="py-2">
                            <select name="dimension2_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- None --</option>
                                @foreach($dimensions as $d)
                                    <option value="{{ $d->id }}" {{ request('dimension2_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @else
                    <input type="hidden" name="dimension2_id" value="">
                    @endif
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap align-top">Memo:</td>
                        <td class="py-2">
                            <textarea name="memo_" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ request('memo_') }}</textarea>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="text-center">
    <button type="submit" name="submit" value="1" class="px-8 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Enter Transfer</button>
</div>

</form>
@endsection