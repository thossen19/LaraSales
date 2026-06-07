@extends('layouts.app')
@section('title', 'Employee Advance Entry')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Advance Entry</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $msg !!}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{!! $error !!}</div>
@endif

<form method="post" action="{{ route('hr.employee-advances') }}">
    @csrf
    <input type="hidden" name="PayType" value="6">
    <input type="hidden" name="NewAdvance" value="1">

    <div id="pmt_header">
        <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
            <tr>
                <td class="border px-3 py-2">
                    <table class="w-full">
                        <tr>
                            <td class="p-1 text-right font-semibold">Date:</td>
                            <td class="p-1"><input type="text" name="date_" value="{{ old('date_', $cart['tran_date'] ?? date('Y-m-d')) }}" size="12" class="border px-2 py-1"></td>
                            <td class="p-1 text-right font-semibold">Reference:</td>
                            <td class="p-1"><input type="text" name="ref" value="{{ old('ref', $cart['reference'] ?? '') }}" size="12" class="border px-2 py-1"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="border px-3 py-2">
                    <table class="w-full">
                        <tr>
                            <td class="p-1 text-right font-semibold w-1/6">Pay To:</td>
                            <td class="p-1">
                                <select name="person_id" class="border px-2 py-1 min-w-[200px]">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" @selected(old('person_id', $cart['person_id'] ?? '') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-1 text-right font-semibold">Bank Account:</td>
                            <td class="p-1">
                                <select name="bank_account" class="border px-2 py-1 min-w-[180px]">
                                    <option value="">Select Bank Account</option>
                                    @foreach($bank_accounts as $ba)
                                        <option value="{{ $ba->id }}" @selected(old('bank_account', $cart['bank_account'] ?? '') == $ba->id)>{{ $ba->bank_account_name }} ({{ $ba->bank_curr_code }})</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-1 text-right font-semibold">Amount:</td>
                            <td class="p-1"><input type="text" name="advance_amount" value="{{ old('advance_amount', $cart['advance_amount'] ?? '') }}" size="12" class="border px-2 py-1"></td>
                            <td colspan="2"></td>
                        </tr>
                    </table>
                    <input type="hidden" name="for_payslip" value="">
                    <input type="hidden" name="PaySlipNo" value="">
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center mb-4">
        <input type="submit" name="update_advances" value="Generate Payment Items" title="Generate Payment GL Items" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
    </div>

    @if(count($cart['gl_items']) > 0 || count($advances) > 0)
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            @if(count($advances) > 0)
                <h3 class="font-bold text-lg mb-2">Allocated amounts:</h3>
                <table class="table-auto border-collapse w-full text-sm mb-4">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-2 py-1">Transaction Type</th>
                            <th class="border px-2 py-1">#</th>
                            <th class="border px-2 py-1">Ref</th>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Amount</th>
                            <th class="border px-2 py-1">Other Allocations</th>
                            <th class="border px-2 py-1">Left to Allocate</th>
                            <th class="border px-2 py-1">This Allocation</th>
                            <th class="border px-2 py-1"></th>
                            <th class="border px-2 py-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $k = 0; @endphp
                        @foreach($advances as $adv)
                            <tr class="{{ $k % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="border px-2 py-1">Bank Payment</td>
                                <td class="border px-2 py-1 text-right">{{ $adv->trans_no }}</td>
                                <td class="border px-2 py-1">{{ $adv->ref ?? '' }}</td>
                                <td class="border px-2 py-1 text-center">{{ $adv->pay_date }}</td>
                                <td class="border px-2 py-1 text-right">{{ number_format($adv->pay_amount, 2) }}</td>
                                <td class="border px-2 py-1 text-right">{{ number_format($adv->amt_allocated, 2) }}</td>
                                <td class="border px-2 py-1 text-right">{{ number_format($adv->remain, 2) }}</td>
                                <td class="border px-2 py-1">
                                    <input type="text" name="amount{{ $adv->id }}" id="amount{{ $adv->id }}" value="{{ old('amount'.$adv->id, '0') }}" size="10" class="border px-1 py-0.5 text-right" onchange="blur_alloc(this)">
                                </td>
                                <td class="border px-2 py-1">
                                    <a href="#" name="Alloc{{ $adv->id }}" onclick="emp_allocate_all(this.name.substr(5));return false;" class="text-blue-600 hover:text-blue-800">All</a>
                                </td>
                                <td class="border px-2 py-1">
                                    <a href="#" name="DeAll{{ $adv->id }}" onclick="emp_allocate_none(this.name.substr(5));return false;" class="text-blue-600 hover:text-blue-800">None</a>
                                    <input type="hidden" name="un_allocated{{ $adv->id }}" id="un_allocated{{ $adv->id }}" value="{{ number_format($adv->remain, 2, '.', '') }}">
                                </td>
                            </tr>
                            @php $k++; @endphp
                        @endforeach
                    </tbody>
                </table>
            @endif

            <h3 class="font-bold text-lg mb-2">Payment Items</h3>
            <div id="items_table">
                <table class="table-auto border-collapse w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-2 py-1">Account Code</th>
                            <th class="border px-2 py-1">Account Description</th>
                            <th class="border px-2 py-1">Debit</th>
                            <th class="border px-2 py-1">Credit</th>
                            <th class="border px-2 py-1">Memo</th>
                            <th class="border px-2 py-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $k = 0; @endphp
                        @foreach($cart['gl_items'] as $line => $item)
                            @if($edit_index !== null && $edit_index == $line)
                                <tr class="bg-yellow-50">
                                    <td class="border px-2 py-1">
                                        <select name="code_id" class="border px-2 py-1 text-xs">
                                            <option value="">Select GL Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->code }}" @selected($item['code_id'] == $acc->code)>{{ $acc->code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border px-2 py-1">
                                        @php $acc = $accounts->firstWhere('code', $item['code_id']); @endphp
                                        {{ $acc->name ?? '' }}
                                        <input type="hidden" name="Index" value="{{ $line }}">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="AmountDebit" value="{{ $item['amount'] > 0 ? number_format($item['amount'], 2, '.', '') : '' }}" size="12" class="border px-1 py-0.5 text-right">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="AmountCredit" value="{{ $item['amount'] < 0 ? number_format(-$item['amount'], 2, '.', '') : '' }}" size="12" class="border px-1 py-0.5 text-right">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="LineMemo" value="{{ $item['memo'] }}" size="25" class="border px-1 py-0.5">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="submit" name="UpdateItem" value="Update" class="bg-green-500 hover:bg-green-600 text-white px-2 py-0.5 rounded text-xs">
                                        <input type="submit" name="CancelItemChanges" value="Cancel" class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-0.5 rounded text-xs">
                                    </td>
                                </tr>
                            @else
                                <tr class="{{ $k % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    @php
                                        $acc = $accounts->firstWhere('code', $item['code_id']);
                                        $desc = $acc->name ?? $item['code_id'];
                                    @endphp
                                    <td class="border px-2 py-1">{{ $item['code_id'] }}</td>
                                    <td class="border px-2 py-1">{{ $desc }}</td>
                                    @if($item['amount'] > 0)
                                        <td class="border px-2 py-1 text-right">{{ number_format($item['amount'], 2) }}</td>
                                        <td class="border px-2 py-1"></td>
                                    @else
                                        <td class="border px-2 py-1"></td>
                                        <td class="border px-2 py-1 text-right">{{ number_format(-$item['amount'], 2) }}</td>
                                    @endif
                                    <td class="border px-2 py-1">{{ $item['memo'] }}</td>
                                    <td class="border px-2 py-1 text-center">
                                        <button type="submit" name="Edit" value="{{ $line }}" class="text-blue-600 hover:text-blue-800 text-xs mr-1">Edit</button>
                                        <button type="submit" name="Delete" value="{{ $line }}" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Remove line from document?')">Delete</button>
                                    </td>
                                </tr>
                                @php $k++; @endphp
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-100">
                            <td colspan="2" class="border px-2 py-1 text-right">Total</td>
                            <td class="border px-2 py-1 text-right" id="item_amount">{{ number_format($total_debit, 2) }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($total_credit, 2) }}</td>
                            <td class="border px-2 py-1" colspan="2"></td>
                        </tr>
                        <input type="hidden" name="total_payments" id="total_payments" value="{{ number_format($total_debit, 2, '.', '') }}">
                        @if($edit_index === null || !isset($cart['gl_items'][$edit_index]))
                        <tr>
                            <td class="border px-2 py-1">
                                <select name="code_id" class="border px-2 py-1 text-xs">
                                    <option value="">Select GL Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->code }}" @selected(old('code_id') == $acc->code)>{{ $acc->code }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="description" value="{{ old('description') }}" size="20" readonly class="border-0 bg-transparent">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="AmountDebit" value="{{ old('AmountDebit') }}" size="12" class="border px-1 py-0.5 text-right">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="AmountCredit" value="{{ old('AmountCredit') }}" size="12" class="border px-1 py-0.5 text-right">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="LineMemo" value="{{ old('LineMemo') }}" size="25" class="border px-1 py-0.5">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="submit" name="AddItem" value="Add Item" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-0.5 rounded text-xs">
                            </td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    @if(count($cart['gl_items']) > 0 || count($advances) > 0)
        <div class="text-center mb-4">
            <textarea name="memo_" rows="3" cols="50" class="border px-2 py-1" placeholder="Memo">{{ old('memo_', $cart['memo_'] ?? '') }}</textarea>
        </div>
        <input type="hidden" name="amount" id="amount" value="{{ old('amount', $total_allocated) }}">
        <div class="text-center space-x-2">
            <input type="submit" name="Update" value="Update" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            @if(count($cart['gl_items']) > 0)
                <input type="submit" name="Process" value="Process Payment" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
            @endif
            <input type="submit" name="CancelOrder" value="Cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
        </div>
    @endif
</form>
@endsection

@push('scripts')
<script>
function blur_alloc(i) {
    function get_amount(name, raw) {
        var el = document.getElementById(name) || document.getElementsByName(name)[0];
        if (!el) return 0;
        var val = parseFloat(el.value.replace(/,/g, '')) || 0;
        return raw ? val : Math.max(0, val);
    }
    function price_format(name, val, dec) {
        var el = document.getElementById(name) || document.getElementsByName(name)[0];
        if (!el) return;
        el.value = val.toFixed(2);
    }

    var change = get_amount(i.name);
    var payment_amt = get_amount('item_amount', true);

    if (i.name != 'amount' && i.name != 'charge' && i.name != 'discount')
        change = Math.min(change, get_amount('maxval' + i.name.substr(6), 1));

    price_format(i.name, change, 2);

    if (i.name != 'amount' && i.name != 'charge') {
        if (change < 0) change = 0;

        change = change - (parseFloat(i.getAttribute('_last')) || 0);

        var total = get_amount('amount') + change;
        price_format('amount', total, 2);
        document.getElementById('amount').value = total.toFixed(2);
        var itemAmt = parseFloat(payment_amt) - change;
        document.getElementById('item_amount').innerHTML = itemAmt.toFixed(2);
        document.getElementById('total_payments').value = itemAmt.toFixed(2);
    }
}

function emp_allocate_all(doc) {
    function get_amount(name, raw) {
        var el = document.getElementById(name) || document.getElementsByName(name)[0];
        if (!el) return 0;
        var val = parseFloat(el.value.replace(/,/g, '')) || 0;
        return raw ? val : Math.max(0, val);
    }
    function price_format(name, val, dec) {
        var el = document.getElementById(name) || document.getElementsByName(name)[0];
        if (!el) return;
        el.value = val.toFixed(2);
    }

    var amount = get_amount('amount' + doc);
    var unallocated = get_amount('un_allocated' + doc);
    var total = get_amount('amount', true);
    var payment_amt = parseFloat(document.getElementById('item_amount').innerHTML.replace(/,/g, '')) || 0;
    var left = 0;
    total -= (amount - unallocated);
    left -= (amount - unallocated);
    amount = unallocated;

    if (left < 0) {
        total += left;
        amount += left;
        left = 0;
    }

    price_format('amount' + doc, amount, 2);
    document.getElementById('amount').value = total.toFixed(2);
    price_format('amount', total, 2);
    document.getElementById('item_amount').innerHTML = (payment_amt - total).toFixed(2);
    document.getElementById('total_payments').value = (payment_amt - total).toFixed(2);
}

function emp_allocate_none(doc) {
    function get_amount(name, raw) {
        var el = document.getElementById(name) || document.getElementsByName(name)[0];
        if (!el) return 0;
        var val = parseFloat(el.value.replace(/,/g, '')) || 0;
        return raw ? val : Math.max(0, val);
    }
    function price_format(name, val, dec) {
        var el = document.getElementById(name) || document.getElementsByName(name)[0];
        if (!el) return;
        el.value = val.toFixed(2);
    }

    var amount = get_amount('amount' + doc);
    var total = get_amount('amount', true);
    var payment_amt = parseFloat(document.getElementById('item_amount').innerHTML.replace(/,/g, '')) || 0;
    price_format('amount' + doc, 0, 2);
    document.getElementById('amount').value = (total - amount).toFixed(2);
    price_format('amount', total - amount, 2);
    document.getElementById('item_amount').innerHTML = (parseFloat(payment_amt) + amount).toFixed(2);
    document.getElementById('total_payments').value = (parseFloat(payment_amt) + amount).toFixed(2);
}
</script>
@endpush
