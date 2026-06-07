@extends('layouts.app')
@section('title', 'Invoice Against Sales Delivery - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Invoice Against Sales Delivery</h2>
    <p class="mt-1 text-sm text-gray-500">Select a completed delivery to generate an invoice.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if($createFromId && $delivery)
    {{-- Invoice Entry Mode --}}
    <form method="POST" action="{{ route('sales.invoice.from-delivery') }}">
    @csrf
    <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-file-invoice mr-2"></i>Create Sales Invoice</h3>
            <div class="text-sm text-indigo-200">
                Delivery: <span class="font-semibold text-white">{{ $delivery->order_number }}</span> |
                Customer: <span class="font-semibold text-white">{{ $delivery->customer->name ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                    <input type="text" value="{{ $delivery->internal_notes ?? '' }}" readonly class="w-full border border-gray-200 bg-gray-50 rounded-md px-3 py-2 text-sm text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sales Type</label>
                    <input type="text" value="{{ $delivery->salesType->type_name ?? 'N/A' }}" readonly class="w-full border border-gray-200 bg-gray-50 rounded-md px-3 py-2 text-sm text-gray-500">
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 mb-6">
                <h4 class="text-md font-medium text-gray-900 mb-3">Delivery Items</h4>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Discount</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $subtotal = 0; @endphp
                            @foreach($delivery->lineItems as $li)
                                @php $lineTotal = $li->quantity * $li->unit_price * (1 - ($li->discount_percentage / 100)); $subtotal += $lineTotal; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $li->item_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $li->description }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li->quantity, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li->unit_price, 4) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li->discount_percentage, 2) }}%</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">{{ number_format($lineTotal, 4) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-end">
                    <div class="w-72 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sub-total:</span>
                            <span class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-indigo-700">${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Notes</label>
                <textarea name="Comments" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $delivery->customer_notes ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-4 mt-6">
        <button type="submit" name="CancelInvoice" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Back to Deliveries</button>
        <button type="submit" name="ConfirmInvoice" value="1" class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-md hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm"><i class="fas fa-file-invoice mr-2"></i>Create Invoice</button>
    </div>
    </form>
@else
    {{-- Search Mode --}}
    <form method="GET" action="{{ route('sales.invoice.from-delivery') }}">
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-search mr-2"></i>Search Delivery</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery #</label>
                    <input type="text" name="delivery_no" value="{{ $searchDeliveryNo }}" placeholder="Partial or full #" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $searchCustomer == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                        <option value="">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->loc_code }}" {{ $searchLocation == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ $searchFrom }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ $searchTo }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
            </div>
            <div class="mt-4 flex items-center gap-4 flex-wrap">
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition shadow-sm text-sm"><i class="fas fa-search mr-2"></i>Search</button>
                <a href="{{ route('sales.invoice.from-delivery', ['outstanding_only' => 0]) }}" class="px-5 py-2 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm text-sm"><i class="fas fa-times mr-2"></i>Show All</a>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
                    <input type="hidden" name="outstanding_only" value="0">
                    <input type="checkbox" name="outstanding_only" value="1" {{ $outstandingOnly ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Show Outstanding Only
                </label>
            </div>
        </div>
    </div>
    </form>

    <form method="POST" action="{{ route('sales.invoice.from-delivery') }}">
    @csrf
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Completed Deliveries</h3>
            <div>
                <button type="submit" name="BatchInvoice" value="1" class="px-4 py-1.5 bg-white text-indigo-700 font-medium rounded-md hover:bg-indigo-50 transition text-sm shadow-sm"><i class="fas fa-file-invoice mr-1"></i>Invoice Selected</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-10">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Delivery #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Delivery Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider"># Items</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deliveries as $d)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="selected_deliveries[]" value="{{ $d->id }}" class="delivery-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $d->status !== 'delivered' ? 'disabled' : '' }}>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $d->order_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $d->internal_notes ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $d->customer_notes ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $d->customer->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $d->delivery_date ? $d->delivery_date->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $d->location ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $d->line_items_count }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">${{ number_format($d->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-0.5">
                                    <a href="{{ route('sales.orders.edit', $d) }}" class="p-1.5 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded transition" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                                    @if($d->status === 'delivered')
                                        <form method="POST" action="{{ route('sales.invoice.from-delivery') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="select_invoice" value="{{ $d->id }}">
                                            <button type="submit" title="Create Invoice" class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition"><i class="fas fa-file-invoice text-xs"></i></button>
                                        </form>
                                    @else
                                        <span class="p-1.5 text-green-600" title="Invoiced"><i class="fas fa-check-circle text-xs"></i></span>
                                    @endif
                                    <a href="{{ route('sales.orders.show', [$d, 'print' => 'delivery']) }}" title="Print" target="_blank" class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition"><i class="fas fa-print text-xs"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-sm text-gray-500 text-center">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                                No deliveries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deliveries->count() > 0)
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-sm text-gray-500">
                <span>Showing {{ $deliveries->count() }} delivery(s)</span>
                <button type="submit" name="BatchInvoice" value="1" class="px-4 py-1.5 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition text-sm shadow-sm"><i class="fas fa-file-invoice mr-1"></i>Invoice Selected</button>
            </div>
        @endif
    </div>
    </form>
@endif

@push('scripts')
<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.delivery-checkbox:not(:disabled)').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection