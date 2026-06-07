@extends('layouts.app')

@section('title', 'Supplier Inquiry - Sales ERP')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Supplier Inquiry</h2>
    </div>

    <form method="GET" action="{{ route('purchases.inquiries.transactions') }}" class="mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select a supplier:</label>
                    <select name="supplier_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="filter_type"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="invoice" {{ $filter_type == 'invoice' ? 'selected' : '' }}>Invoices</option>
                        <option value="credit_note" {{ $filter_type == 'credit_note' ? 'selected' : '' }}>Credit Notes</option>
                        <option value="payment" {{ $filter_type == 'payment' ? 'selected' : '' }}>Payments</option>
                        <option value="overdue" {{ $filter_type == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                @if($filter_type !== '')
                <div>
                    <label class="block text-sm font-medium text-gray-700">From:</label>
                    <input type="date" name="from_date" value="{{ $from_date }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">To:</label>
                    <input type="date" name="to_date" value="{{ $to_date }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                @endif
                <div class="flex gap-2">
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex-1">Search</button>
                    <a href="{{ route('purchases.inquiries.transactions') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Clear</a>
                </div>
            </div>
        </div>
    </form>

    @if($aging)
    <div class="bg-white shadow rounded-lg p-4 mb-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-3 py-2">Currency</th>
                    <th class="text-left px-3 py-2">Terms</th>
                    <th class="text-right px-3 py-2">Current</th>
                    <th class="text-right px-3 py-2">1 - 30 Days</th>
                    <th class="text-right px-3 py-2">31 - 60 Days</th>
                    <th class="text-right px-3 py-2">Over 60 Days</th>
                    <th class="text-right px-3 py-2">Total Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-3 py-2">{{ $aging->curr_code }}</td>
                    <td class="px-3 py-2">{{ $aging->terms }}</td>
                    <td class="text-right px-3 py-2">{{ number_format($aging->current, 2) }}</td>
                    <td class="text-right px-3 py-2">{{ number_format($aging->due_30, 2) }}</td>
                    <td class="text-right px-3 py-2">{{ number_format($aging->due_60, 2) }}</td>
                    <td class="text-right px-3 py-2">{{ number_format($aging->overdue2, 2) }}</td>
                    <td class="text-right px-3 py-2 font-bold">{{ number_format($aging->balance, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($transactions->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">Type</th>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Reference</th>
                        <th class="text-left px-4 py-3">Supplier's Reference</th>
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Due Date</th>
                        <th class="text-right px-4 py-3">Amount</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    @php
                        $is_overdue = $t['due_date'] && $t['due_date'] < date('Y-m-d') && abs($t['balance']) > 0;
                    @endphp
                    <tr class="border-t hover:bg-gray-50 {{ $is_overdue ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">{{ $t['type'] }}</td>
                        <td class="text-right px-4 py-3 font-medium">{{ $t['trans_no'] }}</td>
                        <td class="px-4 py-3">{{ $t['reference'] }}</td>
                        <td class="px-4 py-3">{{ $t['supp_reference'] }}</td>
                        <td class="px-4 py-3">{{ $t['date'] }}</td>
                        <td class="px-4 py-3">{{ $t['due_date'] }}</td>
                        <td class="text-right px-4 py-3 {{ $t['amount'] < 0 ? 'text-red-600' : '' }}">{{ number_format(abs($t['amount']), 2) }}</td>
                        <td class="text-center px-4 py-3 whitespace-nowrap">
                            @if($t['type_code'] == 'invoice' && $t['balance'] > 0)
                                <a href="{{ route('purchases.credit-notes.index') }}?supplier_id={{ $supplier_id }}"
                                   class="text-orange-600 hover:text-orange-800 mx-1" title="Credit This">Credit</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">
                {{ $paginator->links() }}
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-search text-6xl mb-4"></i>
                <p class="text-lg">No transactions found for this supplier.</p>
            </div>
        @endif
    </div>
@endsection
