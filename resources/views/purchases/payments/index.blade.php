@extends('layouts.app')
@section('title', 'Supplier Payment Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Supplier Payment Entry</h2>
    <p class="mt-2 text-gray-600">Record a payment made to a supplier.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('purchases.payments.index') }}" id="payment-form">
@csrf

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment To:</label>
                <select name="supplier_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select a supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Bank Account:</label>
                <select name="bank_account_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select bank account --</option>
                    @foreach($bankAccounts as $ba)
                        <option value="{{ $ba->id }}" {{ $bank_account_id == $ba->id ? 'selected' : '' }}>{{ $ba->bank_account_name }} ({{ $ba->bank_curr_code ?? 'USD' }})</option>
                    @endforeach
                </select>
            </div>
            @if($bankAccount)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Balance:</label>
                <p class="text-sm font-semibold text-gray-900 py-2">--</p>
            </div>
            @endif
        </div>
        <!-- Middle Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Paid:</label>
                <input type="date" name="payment_date" value="{{ $payment_date }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference:</label>
                <input type="text" name="reference" value="{{ $reference }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            @if($show_bank_amount)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Amount ({{ $bank_currency }}):</label>
                <input type="number" name="bank_amount" value="{{ $bank_amount }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Charge ({{ $bank_currency }}):</label>
                <input type="number" name="bank_charge" value="{{ $bank_charge }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <!-- Right Column -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension:</label>
                <select name="dimension_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="0">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $dimension_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 2:</label>
                <select name="dimension2_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="0">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $dimension2_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Allocation Table -->
@if(!empty($outstanding_invoices))
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Allocated amounts in {{ $supplier_currency }}:</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier Ref</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Due Date</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Other Allocations</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Left to Allocate</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">This Allocation</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $total_allocated = 0;
                    $k = 0;
                @endphp
                @foreach($outstanding_invoices as $inv)
                    @php
                        $left = $inv->outstanding_amount;
                        $current_alloc = $allocations[$inv->id] ?? 0;
                        $total_allocated += $current_alloc;
                    @endphp
                    <tr class="{{ $k % 2 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-4 py-3 text-sm text-gray-900">Invoice</td>
                        <td class="px-4 py-3 text-sm text-center text-indigo-600 font-medium">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $inv->supp_reference ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $inv->invoice_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $inv->due_date ? $inv->due_date->format('d/m/Y') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ number_format($inv->alloc, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($left, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            <input type="number" name="alloc_{{ $inv->id }}" value="{{ number_format($current_alloc, 2, '.', '') }}" min="0" step="0.01" class="w-28 border border-gray-300 rounded-md px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500 alloc-input" data-max="{{ $left }}" data-id="{{ $inv->id }}">
                            <div class="inline-flex text-xs ml-1">
                                <a href="javascript:void(0)" class="text-indigo-600 hover:text-indigo-800 alloc-all" data-id="{{ $inv->id }}">All</a>
                                <span class="mx-1 text-gray-400">|</span>
                                <a href="javascript:void(0)" class="text-indigo-600 hover:text-indigo-800 alloc-none" data-id="{{ $inv->id }}">None</a>
                            </div>
                        </td>
                    </tr>
                    @php $k++; @endphp
                @endforeach
                <!-- Totals -->
                <tr class="bg-gray-100 font-medium">
                    <td colspan="8" class="px-4 py-3 text-right text-sm text-gray-700">Total Allocated</td>
                    <td class="px-4 py-3 text-right text-sm text-gray-900" id="total-allocated">{{ number_format($total_allocated, 2) }}</td>
                </tr>
                <tr class="bg-gray-100 font-medium">
                    @php
                        $pay_amt = (float)($amount ?: 0);
                        $left_to_allocate = max(0, $pay_amt - $total_allocated);
                    @endphp
                    <td colspan="8" class="px-4 py-3 text-right text-sm text-gray-700">Left to Allocate</td>
                    <td class="px-4 py-3 text-right text-sm {{ $left_to_allocate < 0 ? 'text-red-600' : 'text-gray-900' }}" id="left-to-allocate">{{ number_format($left_to_allocate, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@elseif($supplier_id)
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="text-center py-8 text-gray-500">
        <p class="text-sm">There are no outstanding invoices for this supplier that have not been paid.</p>
    </div>
</div>
@endif

<!-- Footer -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount of Discount ({{ $supplier_currency }}):</label>
                <input type="number" name="discount" value="{{ $discount }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount of Payment ({{ $supplier_currency }}):</label>
                <input type="number" name="amount" value="{{ $amount }}" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" id="payment-amount">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Memo:</label>
            <textarea name="memo" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ $memo }}</textarea>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex justify-center">
    @if($supplier_id && !empty($outstanding_invoices))
    <button type="submit" name="ProcessPayment" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Enter Payment</button>
    @endif
</div>

</form>
@endsection

@push('scripts')
<script>
// Allocation All/None links
document.querySelectorAll('.alloc-all').forEach(function(link) {
    link.addEventListener('click', function() {
        var id = this.dataset.id;
        var input = document.querySelector('input[name="alloc_' + id + '"]');
        if (input) {
            input.value = input.dataset.max;
            updateTotals();
        }
    });
});
document.querySelectorAll('.alloc-none').forEach(function(link) {
    link.addEventListener('click', function() {
        var id = this.dataset.id;
        var input = document.querySelector('input[name="alloc_' + id + '"]');
        if (input) {
            input.value = '0.00';
            updateTotals();
        }
    });
});
// Update totals on input change
document.querySelectorAll('.alloc-input').forEach(function(input) {
    input.addEventListener('input', updateTotals);
});
document.getElementById('payment-amount') && document.getElementById('payment-amount').addEventListener('input', updateTotals);

function updateTotals() {
    var total = 0;
    document.querySelectorAll('.alloc-input').forEach(function(input) {
        var val = parseFloat(input.value) || 0;
        total += val;
    });
    document.getElementById('total-allocated').textContent = total.toFixed(2);
    var payAmount = parseFloat(document.getElementById('payment-amount').value) || 0;
    var left = payAmount - total;
    var leftEl = document.getElementById('left-to-allocate');
    leftEl.textContent = Math.max(0, left).toFixed(2);
    leftEl.className = 'px-4 py-3 text-right text-sm ' + (left < 0 ? 'text-red-600' : 'text-gray-900');
}
</script>
@endpush
