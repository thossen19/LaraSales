@extends('layouts.app')
@section('title', 'Reconcile Bank Account - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Reconcile Bank Account</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.reconcile') }}">
@csrf

<table class="mb-4">
    <tr>
        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Account:</td>
        <td class="py-2 pr-6">
            <select name="bank_account" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- Select --</option>
                @foreach($bank_accounts as $ba)
                    <option value="{{ $ba->id }}" {{ $bank_account == $ba->id ? 'selected' : '' }}>{{ $ba->bank_account_name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Bank Statement:</td>
        <td class="py-2">
            <select name="bank_date" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- New --</option>
                @foreach($statements as $s)
                    <option value="{{ $s->reconcile_date }}" {{ $bank_date == $s->reconcile_date ? 'selected' : '' }}>{{ $s->reconcile_date }}</option>
                @endforeach
            </select>
        </td>
    </tr>
</table>

<hr class="mb-4">

<div id="summary">
    <table class="w-full max-w-4xl bg-white shadow rounded-lg overflow-hidden mb-6">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reconcile Date</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Beginning<br>Balance</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ending<br>Balance</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Account<br>Total</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Reconciled<br>Amount</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Difference</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-4 py-3">
                    <input type="date" name="reconcile_date" value="{{ $reconcile_date }}" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                </td>
                <td class="px-4 py-3 text-right">
                    <input type="text" name="beg_balance" value="{{ number_format($begBalance, 2) }}" class="w-28 border border-gray-300 rounded-md px-2 py-1 text-sm text-right">
                </td>
                <td class="px-4 py-3 text-right">
                    <input type="text" name="end_balance" value="{{ number_format($endBalance, 2) }}" class="w-28 border border-gray-300 rounded-md px-2 py-1 text-sm text-right">
                </td>
                <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($totalAccount, 2) }}</td>
                <td class="px-4 py-3 text-right text-sm text-gray-700" id="reconciled">{{ number_format($reconciledAmount, 2) }}</td>
                <td class="px-4 py-3 text-right text-sm font-semibold {{ $difference != 0 ? 'text-red-600' : 'text-green-600' }}" id="difference">{{ number_format($difference, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<hr class="mb-4">

@if($bank_account && $transactions->isNotEmpty())
    @php
        $selBank = \DB::table('bank_accounts')->where('id', $bank_account)->first();
    @endphp
    <h3 class="text-lg font-medium text-gray-800 mb-3">{{ $selBank->bank_account_name ?? '' }} - {{ $selBank->bank_curr_code ?? 'USD' }}</h3>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">X</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transactions as $t)
                    @php
                        $refTypes = ['journal' => 'Journal', 'deposit' => 'Deposit', 'payment' => 'Payment', 'transfer' => 'Transfer'];
                        $typeLabel = $refTypes[$t->ref_type] ?? ucfirst($t->ref_type);
                        $debit = $t->amount > 0 ? $t->amount : 0;
                        $credit = $t->amount < 0 ? -$t->amount : 0;
                        $recName = 'rec_' . $t->id;
                        $isReconciled = $t->reconciled ? true : false;
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isReconciled ? 'bg-green-50' : '' }}">
                        <td class="px-3 py-2 text-sm">{{ $typeLabel }}</td>
                        <td class="px-3 py-2 text-sm">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">{{ $t->reference_id }}</a>
                        </td>
                        <td class="px-3 py-2 text-sm">{{ $t->reference }}</td>
                        <td class="px-3 py-2 text-sm">{{ $t->trans_date }}</td>
                        <td class="px-3 py-2 text-sm text-right">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                        <td class="px-3 py-2 text-sm text-right">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $t->memo }}</td>
                        <td class="px-3 py-2 text-center">
                            <input type="hidden" name="last[{{ $t->id }}]" value="{{ $isReconciled ? 1 : 0 }}">
                            <input type="checkbox" name="{{ $recName }}" value="1" {{ $isReconciled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-center space-x-4">
        <button type="submit" name="Reconcile" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Reconcile</button>
        <button type="submit" name="ReconcileAll" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Reconcile All</button>
    </div>
@elseif($bank_account)
    <div class="text-center py-8 text-gray-500">No transactions found for this bank account.</div>
@endif

</form>
@endsection