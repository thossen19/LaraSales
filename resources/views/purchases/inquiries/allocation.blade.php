@extends('layouts.app')

@section('title', 'Supplier Allocation Inquiry - Purchases')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Supplier Allocation Inquiry</h2>
    </div>

    <form method="GET" action="{{ route('purchases.inquiries.allocation') }}" class="mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
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
                        <option value="">All Types</option>
                        <option value="1" {{ $filter_type == '1' ? 'selected' : '' }}>Invoices</option>
                        <option value="2" {{ $filter_type == '2' ? 'selected' : '' }}>Overdue Invoices</option>
                        <option value="3" {{ $filter_type == '3' ? 'selected' : '' }}>Payments</option>
                        <option value="4" {{ $filter_type == '4' ? 'selected' : '' }}>Credit Notes</option>
                        <option value="5" {{ $filter_type == '5' ? 'selected' : '' }}>Overdue Credit Notes</option>
                    </select>
                </div>
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
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="show_settled" value="1" id="show_settled" {{ $show_settled ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="show_settled" class="text-sm font-medium text-gray-700">Show Settled</label>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex-1">Search</button>
                    <a href="{{ route('purchases.inquiries.allocation') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($transactions->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">Type</th>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Reference</th>
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Due Date</th>
                        <th class="text-right px-4 py-3">Debit</th>
                        <th class="text-right px-4 py-3">Credit</th>
                        <th class="text-right px-4 py-3">Allocated</th>
                        <th class="text-right px-4 py-3">Balance</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr class="border-t hover:bg-gray-50 {{ $t['is_overdue'] ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">{{ $t['type'] }}</td>
                        <td class="text-right px-4 py-3 font-medium">{{ $t['trans_no'] }}</td>
                        <td class="px-4 py-3">{{ $t['reference'] }}</td>
                        <td class="px-4 py-3">{{ $t['date'] }}</td>
                        <td class="px-4 py-3 {{ $t['is_overdue'] ? 'text-red-600 font-medium' : '' }}">{{ $t['due_date'] ?: '-' }}</td>
                        <td class="text-right px-4 py-3">{{ $t['debit'] > 0 ? number_format($t['debit'], 2) : '' }}</td>
                        <td class="text-right px-4 py-3">{{ $t['credit'] > 0 ? number_format($t['credit'], 2) : '' }}</td>
                        <td class="text-right px-4 py-3">{{ number_format($t['alloc'], 2) }}</td>
                        <td class="text-right px-4 py-3 font-medium {{ abs($t['balance']) > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($t['balance'], 2) }}</td>
                        <td class="text-center px-4 py-3 whitespace-nowrap">
                            @if($t['type_code'] == 'invoice' && $t['balance'] > 0)
                                <a href="{{ route('purchases.invoices.index') }}?supplier_id={{ $t['supplier_id'] }}"
                                   class="text-blue-600 hover:text-blue-800 mx-1" title="Invoice">Invoice</a>
                                <a href="{{ route('purchases.payments.index') }}?supplier_id={{ $t['supplier_id'] }}"
                                   class="text-green-600 hover:text-green-800 mx-1" title="Payment">Payment</a>
                                <a href="{{ route('purchases.credit-notes.index') }}?supplier_id={{ $t['supplier_id'] }}"
                                   class="text-orange-600 hover:text-orange-800 mx-1" title="Credit">Credit</a>
                            @elseif($t['type_code'] == 'credit_note' && abs($t['balance']) > 0)
                                <a href="{{ route('purchases.payments.index') }}?supplier_id={{ $t['supplier_id'] }}"
                                   class="text-green-600 hover:text-green-800 mx-1" title="Payment">Payment</a>
                            @elseif($t['type_code'] == 'payment' && $t['balance'] > 0)
                                <span class="text-gray-400 text-xs">Unallocated</span>
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