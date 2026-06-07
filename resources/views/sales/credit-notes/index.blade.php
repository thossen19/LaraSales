@extends('layouts.app')
@section('title', 'Customer Credit Note Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Customer Credit Note Entry</h2>
    <p class="mt-1 text-sm text-gray-500">Create a credit note for customer returns, adjustments, or discounts.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('sales.credit-notes.index') }}">
@csrf
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-receipt mr-2"></i>Credit Note Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $cart['customer_id'] == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Credit Date *</label>
                <input type="date" name="credit_date" value="{{ $cart['credit_date'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                <input type="text" name="reference" value="{{ $cart['reference'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm" placeholder="Optional reference">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                <select name="branch_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $cart['branch_id'] == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Original Invoice (Optional)</label>
                <select name="sales_order_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
                    <option value="">-- Select Invoice --</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" {{ $cart['sales_order_id'] == $inv->id ? 'selected' : '' }}>{{ $inv->order_number }} - ${{ number_format($inv->total_amount, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <select name="reason" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
                    <option value="">-- Select Reason --</option>
                    <option value="Goods Return" {{ $cart['reason'] == 'Goods Return' ? 'selected' : '' }}>Goods Return</option>
                    <option value="Damaged Goods" {{ $cart['reason'] == 'Damaged Goods' ? 'selected' : '' }}>Damaged Goods</option>
                    <option value="Wrong Item Shipped" {{ $cart['reason'] == 'Wrong Item Shipped' ? 'selected' : '' }}>Wrong Item Shipped</option>
                    <option value="Price Adjustment" {{ $cart['reason'] == 'Price Adjustment' ? 'selected' : '' }}>Price Adjustment</option>
                    <option value="Discount" {{ $cart['reason'] == 'Discount' ? 'selected' : '' }}>Discount</option>
                    <option value="Other" {{ $cart['reason'] == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-list mr-2"></i>Credit Items</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Item Code</label>
                <select name="stock_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
                    <option value="">-- Select --</option>
                    @foreach($items as $it)
                        <option value="{{ $it->code }}" {{ $cart['stock_id'] == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <input type="text" name="item_description" value="{{ $cart['item_description'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm" placeholder="Description">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
                <input type="number" name="qty" value="{{ $cart['qty'] }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Unit Price</label>
                <input type="number" name="price" value="{{ $cart['price'] }}" step="0.01" min="0" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Discount %</label>
                <div class="flex gap-2">
                    <input type="number" name="discount" value="{{ $cart['discount'] }}" step="0.01" min="0" max="100" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-sm">
                    <button type="submit" name="AddItem" value="1" class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition whitespace-nowrap"><i class="fas fa-plus mr-1"></i>Add</button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Discount %</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-16">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $subtotal = 0; @endphp
                    @forelse($cart['line_items'] as $idx => $li)
                        @php $lineTotal = $li['quantity'] * $li['unit_price'] * (1 - $li['discount_percent']); $subtotal += $lineTotal; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $li['stock_id'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $li['description'] }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li['quantity'], 4) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li['unit_price'], 4) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($li['discount_percent'] * 100, 2) }}%</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">{{ number_format($lineTotal, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <button type="submit" name="DeleteItem" value="{{ $idx }}" class="text-red-600 hover:text-red-900 p-1" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-sm text-gray-500 text-center">
                                <i class="fas fa-inbox text-gray-300 text-3xl mb-2 block"></i>
                                No items added yet. Use the form above to add items.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($cart['line_items']) > 0)
        <div class="flex justify-end mt-4">
            <div class="w-72 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sub-total:</span>
                    <span class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                    <span class="text-gray-900">Total Credit:</span>
                    <span class="text-orange-700">${{ number_format($subtotal, 2) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Memo / Notes</label>
    <textarea name="memo" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">{{ $cart['memo'] }}</textarea>
</div>

<div class="flex justify-center gap-4 mt-6">
    <button type="submit" name="CancelCredit" value="1" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm"><i class="fas fa-times mr-2"></i>Cancel</button>
    <button type="submit" name="ProcessCredit" value="1" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-md hover:from-orange-700 hover:to-orange-800 transition shadow-sm"><i class="fas fa-receipt mr-2"></i>Create Credit Note</button>
</div>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mt-8">
    <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700">
        <h3 class="text-lg font-semibold text-white"><i class="fas fa-history mr-2"></i>Recent Credit Notes</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Credit Note #</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentCredits as $cn)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $cn->credit_note_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $cn->customer->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $cn->credit_date ? $cn->credit_date->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $cn->reason ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 font-medium">${{ number_format($cn->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ ucfirst($cn->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-sm text-gray-500 text-center">No credit notes recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('select[name="customer_id"]')?.addEventListener('change', function() {
    this.closest('form').submit();
});
document.querySelector('select[name="sales_order_id"]')?.addEventListener('change', function() {
    this.closest('form').submit();
});
document.querySelector('select[name="stock_id"]')?.addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var name = selected.text.split(' - ').slice(1).join(' - ');
    document.querySelector('input[name="item_description"]').value = name || '';
});
</script>
@endpush
@endsection
