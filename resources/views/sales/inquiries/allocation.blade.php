@extends('layouts.app')
@section('title', 'Customer Allocation Inquiry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Customer Allocation Inquiry</h2>
    <p class="mt-1 text-sm text-gray-500">View invoice, payment, and credit note allocations with outstanding balances.</p>
</div>

<form method="GET" action="{{ route('sales.inquiries.allocation') }}">
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-filter mr-2"></i>Select Customer and Filters</h3>
    </div>
    <div class="p-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select a customer:</label>
                <select name="customer_id" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm min-w-[220px]">
                    <option value="all">All Customers</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $selectedCustomer == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From:</label>
                <input type="date" name="TransAfterDate" value="{{ $dateFrom }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To:</label>
                <input type="date" name="TransToDate" value="{{ $dateTo }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type:</label>
                <select name="filterType" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                    <option value="all" {{ $filterType == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="0" {{ $filterType == '0' ? 'selected' : '' }}>Invoice</option>
                    <option value="3" {{ $filterType == '3' ? 'selected' : '' }}>Delivery</option>
                    <option value="4" {{ $filterType == '4' ? 'selected' : '' }}>Payment</option>
                    <option value="6" {{ $filterType == '6' ? 'selected' : '' }}>Credit Note</option>
                    <option value="7" {{ $filterType == '7' ? 'selected' : '' }}>Journal Entry</option>
                </select>
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="showSettled" value="1" {{ $showSettled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Show settled
                </label>
            </div>
            <div>
                <button type="submit" name="RefreshInquiry" value="1" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm"><i class="fas fa-search mr-1"></i>Search</button>
            </div>
        </div>
    </div>
</div>
</form>

@if(request()->anyFilled(['customer_id', 'TransAfterDate', 'TransToDate', 'filterType']))
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Transactions</h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Currency</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Debit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Credit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Allocated</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paginated as $t)
                        @php
                            $isOverdue = $t['overdue'];
                            $debitAmount = in_array($t['type'], [7, 5, 6]) ? -$t['total_amount'] : $t['total_amount'];
                            $creditAmount = !in_array($t['type'], [7, 5, 6]) ? -$t['total_amount'] : $t['total_amount'];
                            // Allocation link logic matching FA
                            $showAllocLink = false;
                            $showPaymentLink = false;
                            if ($t['type'] == 7 && $t['total_amount'] > 0) { // Credit Note with positive amount
                                $showAllocLink = true;
                            } elseif ($t['type'] == 8 && $t['total_amount'] < 0) { // Journal with negative amount
                                $showAllocLink = true;
                            } elseif (in_array($t['type'], [5, 6]) && $t['total_amount'] >= $t['allocated']) { // Payment/Deposit
                                $showAllocLink = true;
                            } elseif ($t['type'] == 1 && $t['balance'] > 0) { // Invoice with outstanding
                                $showPaymentLink = true;
                            } elseif ($t['type'] == 8 && abs($t['total_amount']) - $t['allocated'] > 0) { // Journal with outstanding
                                $showPaymentLink = true;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $isOverdue ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $t['type_class'] }}">{{ $t['type_label'] }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                @if($t['url_view'])
                                    <a href="{{ $t['url_view'] }}" class="text-blue-600 hover:text-blue-800 font-medium" target="_blank">{{ $t['trans_no'] }}</a>
                                @else
                                    <span class="text-gray-500">{{ $t['trans_no'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $t['reference'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500">{{ $t['order_'] ? '#' . $t['order_'] : '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $t['date'] ? \Carbon\Carbon::parse($t['date'])->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-700' }}">{{ $t['due_date'] ? \Carbon\Carbon::parse($t['due_date'])->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $t['customer_name'] ?: '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-700">{{ $t['currency'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right {{ $debitAmount > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">{{ $debitAmount > 0 ? '$' . number_format($debitAmount, 2) : '' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right {{ $creditAmount > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">{{ $creditAmount > 0 ? '$' . number_format($creditAmount, 2) : '' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">${{ number_format($t['allocated'], 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium {{ $t['balance'] > 0 ? 'text-red-700' : 'text-green-700' }}">${{ number_format(abs($t['balance']), 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($showAllocLink)
                                        <a href="{{ route('sales.allocations.customer-allocate', ['trans_no' => $t['trans_no'], 'trans_type' => $t['type'], 'debtor_no' => $t['debtor_no']]) }}" class="px-2 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded transition" title="Allocation"><i class="fas fa-link mr-1"></i>Allocation</a>
                                    @endif
                                    @if($showPaymentLink)
                                        <a href="{{ route('sales.payments.index') }}?customer_id={{ $t['debtor_no'] }}&SInvoice={{ $t['trans_no'] }}&Type={{ $t['type'] }}" class="px-2 py-1 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded transition" title="Payment"><i class="fas fa-money-bill mr-1"></i>Payment</a>
                                    @endif
                                    @if($t['url_view'])
                                        <a href="{{ $t['url_view'] }}" class="p-1.5 text-gray-500 hover:text-gray-700 rounded hover:bg-gray-100 transition" title="View" target="_blank"><i class="fas fa-eye text-xs"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($isOverdue)
                        <tr class="bg-red-50">
                            <td colspan="13" class="px-4 py-1 text-xs text-red-500 italic">Marked items are overdue.</td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="13" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-exchange-alt text-5xl mb-3 text-gray-300"></i>
                                <p class="text-base font-medium text-gray-400">No transactions found</p>
                                <p class="text-sm mt-1">Select filters and click "Search".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginated->hasPages())
            <div class="mt-6">
                {{ $paginated->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@else
<div class="bg-white shadow rounded-lg p-12 text-center">
    <i class="fas fa-exchange-alt text-6xl mb-4 text-gray-300"></i>
    <h3 class="text-lg font-medium text-gray-500 mb-2">Select Filters to View Allocations</h3>
    <p class="text-gray-400">Choose a customer, date range, or transaction type above and click "Search".</p>
</div>
@endif

@push('styles')
<style>
    .pagination { display: flex; justify-content: center; gap: 4px; flex-wrap: wrap; }
    .pagination .page-item { list-style: none; }
    .pagination .page-link { display: block; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; font-size: 14px; text-decoration: none; transition: all 0.15s; }
    .pagination .page-link:hover { background-color: #f3f4f6; }
    .pagination .active .page-link { background-color: #4f46e5; border-color: #4f46e5; color: white; }
    .pagination .disabled .page-link { color: #9ca3af; pointer-events: none; background-color: #f9fafb; }
    .pagination svg { width: 16px; height: 16px; }
</style>
@endpush
@endsection
