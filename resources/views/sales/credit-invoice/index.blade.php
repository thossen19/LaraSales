@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#CreditDate', { dateFormat: 'Y-m-d' });
</script>
@endpush
@extends('layouts.app')
@section('title', 'Credit all or part of an Invoice')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Credit all or part of an Invoice</h2>
    <p class="mt-1 text-sm text-gray-500">Select items and quantities to credit against an invoice.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('sales.credit-invoice') }}">
@csrf
<input type="hidden" name="cart_id" value="{{ $cart['invoice_id'] ?? '' }}">

<table class="w-full mb-6 border-collapse bg-white shadow rounded-lg overflow-hidden">
    <tr>
        <td class="p-4 align-top w-1/2">
            <table class="w-full">
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1 w-32">Customer:</td>
                    <td class="py-1">{{ $cart['customer_name'] }}</td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Branch:</td>
                    <td class="py-1">{{ $cart['branch_name'] ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Currency:</td>
                    <td class="py-1">{{ $cart['currency'] }}</td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Reference:</td>
                    <td class="py-1">
                        @if($cart['modify_id'])
                            <span class="text-gray-900">{{ $cart['reference'] }}</span>
                        @else
                            <input type="text" name="ref" value="{{ $cart['reference'] }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-48">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Crediting Invoice:</td>
                    <td class="py-1">
                        <a href="{{ route('sales.orders.show', $cart['invoice_id']) }}" class="text-blue-600 hover:text-blue-800 underline text-sm" target="_blank">#{{ $cart['invoice_id'] }}</a>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Shipping Company:</td>
                    <td class="py-1">
                        <select name="ShipperID" class="border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">--</option>
                            @foreach($shippers as $s)
                                <option value="{{ $s->shipper_name }}" {{ $cart['shipper_id'] == $s->shipper_name ? 'selected' : '' }}>{{ $s->shipper_name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>
        </td>
        <td class="p-4 align-top w-1/2">
            <table class="w-full">
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1 w-40">Invoice Date:</td>
                    <td class="py-1">{{ $cart['invoice_date'] ? \Carbon\Carbon::parse($cart['invoice_date'])->format('d/m/Y') : '' }}</td>
                </tr>
                <tr>
                    <td class="font-bold text-gray-700 pr-4 py-1">Credit Note Date:</td>
                    <td class="py-1">
                        <input type="text" id="CreditDate" name="CreditDate" value="{{ $cart['credit_date'] }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-40">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="credit_items">
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Credit Items</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Description</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Invoiced Quantity</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Units</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Credit Quantity</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Price</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Discount %</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $k = 0; $subtotal = 0; @endphp
                @foreach($cart['line_items'] as $idx => $li)
                    @if($li['invoiced_qty'] > 0 && $li['credit_qty'] <= 0 && $li['invoiced_qty'] == ($li['already_credited'] ?? 0))
                        @continue
                    @endif
                    @php
                        $lineTotal = $li['credit_qty'] * $li['unit_price'] * (1 - $li['discount_percent'] / 100);
                        $subtotal += $lineTotal;
                        $rowClass = $k % 2 == 0 ? 'bg-white' : 'bg-gray-50';
                        $k++;
                    @endphp
                    <tr class="{{ $rowClass }} hover:bg-gray-100 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $li['stock_id'] }}</td>
                        <td class="px-4 py-3 text-sm">
                            <input type="text" name="Line{{ $idx }}Desc" value="{{ $li['description'] }}" class="border-0 bg-transparent text-gray-700 w-full focus:outline-none">
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li['invoiced_qty'], 4) }}</td>
                        <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $li['units'] }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            @if($li['invoiced_qty'] > 0 && $li['credit_qty'] <= 0 && $li['invoiced_qty'] == ($li['already_credited'] ?? 0))
                                <span class="text-gray-400 italic">Fully Credited</span>
                            @else
                                <input type="text" name="Line{{ $idx }}" value="{{ number_format($li['credit_qty'], 4, '.', '') }}" class="border border-gray-300 rounded px-2 py-0.5 text-right w-24 text-sm">
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li['unit_price'], 4) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li['discount_percent'], 2) }}%</td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200">
        <div class="w-80 ml-auto space-y-2">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-700 font-medium">Credit Shipping Cost:</span>
                <input type="text" name="ChargeFreightCost" value="{{ number_format($cart['freight_cost'], 2, '.', '') }}" class="border border-gray-300 rounded px-2 py-1 text-right w-28 text-sm">
            </div>
            @php
                $itemsTotal = array_sum(array_map(function($li) {
                    return $li['credit_qty'] * $li['unit_price'] * (1 - $li['discount_percent'] / 100);
                }, $cart['line_items']));
                $subtotalDisplay = $itemsTotal + $cart['freight_cost'];
            @endphp
            <div class="flex justify-between text-sm">
                <span class="text-gray-700 font-medium">Sub-total:</span>
                <span class="font-medium text-gray-900">${{ number_format($subtotalDisplay, 2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                <span class="text-gray-900">Credit Note Total:</span>
                <span class="text-orange-700">${{ number_format($subtotalDisplay, 2) }}</span>
            </div>
        </div>
    </div>
</div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="font-bold text-gray-700 pr-4 py-2 w-48">Credit Note Type:</td>
                        <td class="py-2">
                            <select name="CreditType" class="border border-gray-300 rounded px-2 py-1 text-sm" onchange="this.closest('form').querySelector('[name=Update]').click();">
                                <option value="Return" {{ ($cart['credit_type'] ?? 'Return') == 'Return' ? 'selected' : '' }}>Return</option>
                                <option value="Write Off" {{ ($cart['credit_type'] ?? '') == 'Write Off' ? 'selected' : '' }}>Write Off</option>
                            </select>
                        </td>
                    </tr>
                    @if(($cart['credit_type'] ?? 'Return') == 'Return')
                    <tr>
                        <td class="font-bold text-gray-700 pr-4 py-2">Items Returned to Location:</td>
                        <td class="py-2">
                            <select name="Location" class="border border-gray-300 rounded px-2 py-1 text-sm w-64">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->location_code ?? $loc->location_name }}" {{ $cart['return_location'] == ($loc->location_code ?? $loc->location_name) ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td class="font-bold text-gray-700 pr-4 py-2">Write off the cost of the items to:</td>
                        <td class="py-2">
                            <select name="WriteOffGLCode" class="border border-gray-300 rounded px-2 py-1 text-sm w-64">
                                <option value="">-- Select GL Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->code }}" {{ $cart['write_off_gl'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            <div>
                <label class="font-bold text-gray-700 block mb-2">Memo:</label>
                <textarea name="CreditText" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ $cart['memo'] }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="Update" value="1" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition shadow-sm"><i class="fas fa-sync mr-2"></i>Update</button>
    <button type="submit" name="ProcessCredit" value="1" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-md hover:from-orange-700 hover:to-orange-800 transition shadow-sm"><i class="fas fa-receipt mr-2"></i>Process Credit Note</button>
</div>
</form>
@endsection
