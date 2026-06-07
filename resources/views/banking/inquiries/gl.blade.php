@extends('layouts.app')
@section('title', 'General Ledger Inquiry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">General Ledger Inquiry</h2>
</div>

<form method="POST" action="{{ route('banking.inquiries.gl') }}">
@csrf
<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Account:</td>
        <td class="py-1 pr-4">
            <select name="account" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All Accounts --</option>
                @foreach($gl_accounts as $a)
                    <option value="{{ $a->code }}" {{ $account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">from:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransFromDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">to:</td>
        <td class="py-1">
            <input type="date" name="TransToDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
    </tr>
</table>
<table class="mb-4">
    <tr>
        @if($use_dimension >= 1)
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 1:</td>
        <td class="py-1 pr-4">
            <select name="Dimension" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value=""> </option>
                @foreach($dimensions as $d)
                    <option value="{{ $d->id }}" {{ $dimension == $d->id ? 'selected' : '' }}>{{ $d->code ? $d->code . ' - ' : '' }}{{ $d->name }}</option>
                @endforeach
            </select>
        </td>
        @endif
        @if($use_dimension > 1)
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 2:</td>
        <td class="py-1 pr-4">
            <select name="Dimension2" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value=""> </option>
                @foreach($dimensions as $d)
                    <option value="{{ $d->id }}" {{ $dimension2 == $d->id ? 'selected' : '' }}>{{ $d->code ? $d->code . ' - ' : '' }}{{ $d->name }}</option>
                @endforeach
            </select>
        </td>
        @endif
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Memo:</td>
        <td class="py-1 pr-4">
            <input type="text" name="Memo" value="{{ $memo }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Amount min:</td>
        <td class="py-1 pr-4">
            <input type="text" name="amount_min" value="{{ $amountMin > 0 ? $amountMin : '' }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-24 text-right">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Amount max:</td>
        <td class="py-1 pr-4">
            <input type="text" name="amount_max" value="{{ $amountMax > 0 ? $amountMax : '' }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-24 text-right">
        </td>
        <td class="py-1">
            <button type="submit" name="Show" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Show</button>
        </td>
    </tr>
</table>
</form>

@if(request('Show') || request('account'))
<hr class="mb-4">

@if($account)
    <div class="mb-2 text-sm font-semibold text-gray-800">{{ $account }} &nbsp;&nbsp;&nbsp; {{ optional(\DB::table('accounts')->where('code', $account)->first())->name }}</div>
@endif

@php
    $dim = (int) $use_dimension;
    $colspan = ($dim == 2 ? '7' : ($dim == 1 ? '6' : '5'));
    $hasAccount = $account !== '';
@endphp

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    @if($dim >= 1)<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 1</th>@endif
                    @if($dim > 1)<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 2</th>@endif
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person/Item</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    @if($showBalances)<th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>@endif
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @if($showBalances)
                <tr class="bg-yellow-50">
                    <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="{{ $colspan }}">Opening Balance - {{ $fromDate }}</td>
                    @php
                        $ob = $openingBalance;
                    @endphp
                    @if($ob >= 0)
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($ob, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                    @else
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format(abs($ob), 2) }}</td>
                    @endif
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2"></td>
                </tr>
                @endif

                @forelse($transactions as $i => $t)
                    @if($loop->iteration % 12 == 1 && !$loop->first)
            </tbody>
        </table>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    @if($dim >= 1)<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 1</th>@endif
                    @if($dim > 1)<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 2</th>@endif
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person/Item</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    @if($showBalances)<th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>@endif
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                    @endif

                    @php
                        $amount = $t->debit_amount - $t->credit_amount;
                        $typeLabels = ['journal' => 'Journal Entry', 'deposit' => 'Bank Deposit', 'payment' => 'Bank Payment', 'transfer' => 'Bank Transfer', 'accrual' => 'Accrual'];
                        $typeLabel = $typeLabels[$t->reference_type] ?? ucfirst($t->reference_type);
                        $memoText = $t->line_description ?: $t->je_description;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $typeLabel }}</td>
                        <td class="px-3 py-2 text-sm text-center"><a href="#" class="text-indigo-600 hover:text-indigo-900">{{ $t->je_id }}</a></td>
                        <td class="px-3 py-2 text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900">{{ $t->entry_number }}</a></td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $t->entry_date }}</td>
                        @if(!$hasAccount)
                            <td class="px-3 py-2 text-sm text-gray-600">{{ $t->account_code }} {{ $t->account_name }}</td>
                        @endif
                        @if($dim >= 1)<td class="px-3 py-2 text-sm text-gray-500">-</td>@endif
                        @if($dim > 1)<td class="px-3 py-2 text-sm text-gray-500">-</td>@endif
                        <td class="px-3 py-2 text-sm text-gray-500">{{ $t->user_name ?? '' }}</td>
                        @if($amount >= 0)
                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($amount, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        @else
                            <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format(abs($amount), 2) }}</td>
                        @endif
                        @if($showBalances)
                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($t->running_balance, 2) }}</td>
                        @endif
                        <td class="px-3 py-2 text-sm text-gray-600 max-w-xs truncate">{{ $memoText }}</td>
                        <td class="px-3 py-2 text-center text-sm">
                            @if($t->reference_type == 'journal')
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 6 + ($dim >= 1 ? 1 : 0) + ($dim > 1 ? 1 : 0) + ($hasAccount ? 0 : 1) + ($showBalances ? 1 : 0) }}" class="px-3 py-8 text-center text-gray-500">No general ledger transactions have been created for the specified criteria.</td>
                    </tr>
                @endforelse

                @if($showBalances && $transactions->count() > 0)
                <tr class="bg-yellow-50">
                    <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="{{ $colspan }}">Ending Balance - {{ $toDate }}</td>
                    @if($runningBalance >= 0)
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($runningBalance, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                    @else
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format(abs($runningBalance), 2) }}</td>
                    @endif
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2 text-sm text-gray-500"></td>
                    <td class="px-3 py-2"></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection