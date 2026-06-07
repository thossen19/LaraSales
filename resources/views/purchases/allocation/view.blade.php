@extends('layouts.app')

@section('title', 'Allocation Detail - Sales ERP')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Allocation Detail</h2>
    </div>

    <div class="bg-white shadow rounded-lg p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm font-medium text-gray-500">Payment</span>
                <p class="text-gray-900">{{ $allocation->payment->payment_number ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Payment Date</span>
                <p class="text-gray-900">{{ $allocation->payment->payment_date ? $allocation->payment->payment_date->format('Y-m-d') : 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Supplier</span>
                <p class="text-gray-900">{{ $allocation->payment->supplier->name ?? $allocation->invoice->supplier->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Bank Account</span>
                <p class="text-gray-900">{{ $allocation->payment->bankAccount->bank_account_name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Invoice</span>
                <p class="text-gray-900">{{ $allocation->invoice->invoice_number ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Invoice Date</span>
                <p class="text-gray-900">{{ $allocation->invoice->invoice_date ? $allocation->invoice->invoice_date->format('Y-m-d') : 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Amount Allocated</span>
                <p class="text-lg font-bold text-gray-900">{{ number_format($allocation->amount, 2) }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Invoice Total</span>
                <p class="text-gray-900">{{ number_format($allocation->invoice->total_amount ?? 0, 2) }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Invoice Outstanding</span>
                <p class="text-gray-900">{{ number_format($allocation->invoice->outstanding_amount ?? 0, 2) }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Created</span>
                <p class="text-gray-900">{{ $allocation->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        @if($allocation->invoice->items->count() > 0)
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Invoice Items</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-3 py-2">Item</th>
                        <th class="text-right px-3 py-2">Qty</th>
                        <th class="text-right px-3 py-2">Price</th>
                        <th class="text-right px-3 py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allocation->invoice->items as $item)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $item->description ?: $item->stock_id }}</td>
                        <td class="text-right px-3 py-2">{{ $item->quantity }}</td>
                        <td class="text-right px-3 py-2">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right px-3 py-2">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <a href="{{ route('purchases.allocation.index') }}"
               class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Back to List</a>
        </div>
    </div>
@endsection