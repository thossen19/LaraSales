@extends('layouts.app')

@section('title', 'Supplier Allocations - Sales ERP')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Supplier Allocations</h2>
        <p class="mt-2 text-gray-600">View and manage supplier payment allocations.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('purchases.allocation.index') }}" class="mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <select name="supplier_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">From</label>
                    <input type="date" name="from_date" value="{{ $from_date }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">To</label>
                    <input type="date" name="to_date" value="{{ $to_date }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Search</button>
                </div>
                <div>
                    <a href="{{ route('purchases.allocation.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 block text-center">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($allocations->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Payment #</th>
                        <th class="text-left px-4 py-3">Payment Type</th>
                        <th class="text-left px-4 py-3">Supplier</th>
                        <th class="text-left px-4 py-3">Invoice #</th>
                        <th class="text-right px-4 py-3">Amount</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allocations as $alloc)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $alloc->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $alloc->payment->payment_number ?? 'N/A' }}</td>
                        <td class="px-4 py-3">Payment</td>
                        <td class="px-4 py-3">{{ $alloc->payment->supplier->name ?? $alloc->invoice->supplier->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $alloc->invoice->invoice_number ?? 'N/A' }}</td>
                        <td class="text-right px-4 py-3">{{ number_format($alloc->amount, 2) }}</td>
                        <td class="text-center px-4 py-3 space-x-2">
                            <a href="{{ route('purchases.allocation.view', $alloc->id) }}"
                               class="text-blue-600 hover:text-blue-800">View</a>
                            <form method="POST" action="{{ route('purchases.allocation.index') }}"
                                  onsubmit="return confirm('Delete this allocation?');"
                                  class="inline">
                                @csrf
                                <input type="hidden" name="supplier_id" value="{{ $supplier_id }}">
                                <input type="hidden" name="from_date" value="{{ $from_date }}">
                                <input type="hidden" name="to_date" value="{{ $to_date }}">
                                <button type="submit" name="delete_id" value="{{ $alloc->id }}"
                                        class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 border-t">
                {{ $allocations->links() }}
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-link text-6xl mb-4"></i>
                <p class="text-lg">No allocations found.</p>
            </div>
        @endif
    </div>
@endsection