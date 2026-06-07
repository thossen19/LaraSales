@extends('layouts.app')
@section('title', 'Bank Account Inquiry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Bank Account Inquiry</h2>
</div>

<form method="POST" action="{{ route('banking.inquiries.bank-account') }}">
@csrf
<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Account:</td>
        <td class="py-1 pr-4">
            <select name="bank_account" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- Select Bank Account --</option>
                @foreach($bankAccounts as $ba)
                    <option value="{{ $ba->id }}" {{ $bankAccount == $ba->id ? 'selected' : '' }}>{{ $ba->name }} - {{ $ba->bank_curr_code }} ({{ $ba->bank_name ?? 'N/A' }})</option>
                @endforeach
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransAfterDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">To:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransToDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1">
            <button type="submit" name="Show" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Show</button>
        </td>
    </tr>
</table>
</form>

@if(request('Show'))
<div id="trans_tbl">
    @if($bankAct)
    <div class="mb-3 text-sm font-semibold text-gray-800">{{ $bankAct->name }} - {{ $bankAct->bank_curr_code }}</div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person/Item</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">GL</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-yellow-50 font-bold">
                        <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="4">Opening Balance - {{ $fromDate }}</td>
                        @if($openingBalance >= 0)
                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($openingBalance, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        @else
                            <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format(abs($openingBalance), 2) }}</td>
                        @endif
                        <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                        <td class="px-3 py-2 text-sm text-gray-500"></td>
                        <td class="px-3 py-2 text-sm text-gray-500"></td>
                        <td class="px-3 py-2"></td>
                        <td class="px-3 py-2"></td>
                    </tr>

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
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Person/Item</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">GL</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                        @endif

                        @php
                            $typeLabels = ['journal' => 'Journal Entry', 'deposit' => 'Bank Deposit', 'payment' => 'Bank Payment', 'transfer' => 'Bank Transfer', 'accrual' => 'Accrual'];
                            $typeLabel = $typeLabels[$t->ref_type] ?? ucfirst($t->ref_type);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $typeLabel }}</td>
                            <td class="px-3 py-2 text-sm text-center"><a href="#" class="text-indigo-600 hover:text-indigo-900">{{ $t->reference_id ?? '-' }}</a></td>
                            <td class="px-3 py-2 text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900">{{ $t->reference }}</a></td>
                            <td class="px-3 py-2 text-sm text-gray-900">{{ $t->trans_date }}</td>
                            @if($t->amount >= 0)
                                <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($t->amount, 2) }}</td>
                                <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                            @else
                                <td class="px-3 py-2 text-sm text-right text-gray-700"></td>
                                <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format(abs($t->amount), 2) }}</td>
                            @endif
                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($t->running_balance, 2) }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ optional(\DB::table('users')->find($t->created_by))->name ?? '--' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-600 max-w-xs truncate">{{ $t->memo ?? '' }}</td>
                            <td class="px-3 py-2 text-center text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900">GL</a></td>
                            <td class="px-3 py-2 text-center text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-3 py-8 text-center text-gray-500">No bank account transactions found for the specified criteria.</td>
                        </tr>
                    @endforelse

                    <tr class="bg-yellow-50 font-bold">
                        <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="4">Ending Balance - {{ $toDate }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($totalDebit, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($totalCredit, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($totalDebit + $totalCredit, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-gray-500"></td>
                        <td class="px-3 py-2 text-sm text-gray-500"></td>
                        <td class="px-3 py-2"></td>
                        <td class="px-3 py-2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection