@extends('layouts.app')
@section('title', 'Payment Advice')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Payment Advice</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $msg !!}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{!! $error !!}</div>
@endif

<form method="post" action="{{ url()->current() }}" enctype="multipart/form-data">
    @csrf

    {{-- Top fields: Bank Account, Date, Reference, Memo --}}
    <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4">
        <tr>
            <td class="border px-3 py-2 w-1/3">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold w-1/3">Bank Account:</td>
                        <td class="p-1">
                            <select name="bank_account_id" class="border px-2 py-1 min-w-[200px]">
                                <option value="">Select Bank Account</option>
                                @foreach($bank_accounts as $ba)
                                    <option value="{{ $ba->id }}" @selected($cart['bank_account_id'] == $ba->id)>{{ $ba->bank_account_name }} ({{ $ba->bank_curr_code }})</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="border px-3 py-2 w-2/3">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Date:</td>
                        <td class="p-1"><input type="text" name="date_" value="{{ $cart['pay_date'] }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Reference:</td>
                        <td class="p-1"><input type="text" name="ref" value="{{ $cart['ref'] }}" size="15" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Memo:</td>
                        <td class="p-1"><input type="text" name="memo_" value="{{ $cart['memo_'] }}" size="20" class="border px-2 py-1"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Payslip Selector --}}
    @if($unpaid_payslips->isNotEmpty())
        <div class="bg-white shadow rounded-lg mb-4 p-2">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-2 py-1">Payslip No</th>
                        <th class="border px-2 py-1">Employee</th>
                        <th class="border px-2 py-1">Date</th>
                        <th class="border px-2 py-1">From</th>
                        <th class="border px-2 py-1">To</th>
                        <th class="border px-2 py-1 text-right">Amount</th>
                        <th class="border px-2 py-1 text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unpaid_payslips as $ps)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }} {{ $cart['payslip_no'] == $ps->payslip_no ? 'bg-blue-100' : '' }}">
                            <td class="border px-2 py-1">{{ $ps->payslip_no }}</td>
                            <td class="border px-2 py-1">{{ $ps->emp_name }}</td>
                            <td class="border px-2 py-1">{{ $ps->generated_date }}</td>
                            <td class="border px-2 py-1">{{ $ps->from_date }}</td>
                            <td class="border px-2 py-1">{{ $ps->to_date }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($ps->payable_amount, 2) }}</td>
                            <td class="border px-2 py-1 text-center">
                                <button type="submit" name="SelectPayslip" value="{{ $ps->payslip_no }}" class="text-blue-600 hover:text-blue-800 text-xs {{ $cart['payslip_no'] == $ps->payslip_no ? 'font-bold underline' : '' }}">{{ $cart['payslip_no'] == $ps->payslip_no ? 'Selected' : 'Select' }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white shadow rounded-lg mb-4 p-4 text-center text-gray-500">No unpaid payslips available.</div>
    @endif

    {{-- Selected Payslip Details --}}
    @if($cart['payslip_no'] > 0)
        <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4">
            <tr>
                <td class="border px-3 py-2">
                    <table class="w-full">
                        <tr>
                            <td class="p-1 text-right font-semibold w-1/4">Payslip #:</td>
                            <td class="p-1 font-bold">{{ $cart['payslip_no'] }}</td>
                            <td class="p-1 text-right font-semibold w-1/4">Employee:</td>
                            <td class="p-1 font-bold">{{ $cart['person_name'] }}</td>
                        </tr>
                        <tr>
                            <td class="p-1 text-right font-semibold w-1/4">Pay Amount:</td>
                            <td class="p-1 font-bold">{{ number_format($cart['pay_amount'], 2) }}</td>
                            <td class="p-1 text-right font-semibold w-1/4">To the order of:</td>
                            <td class="p-1 font-bold">{{ $cart['person_name'] }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Generate GL Items button --}}
        <div class="text-center mb-4">
            <button type="submit" name="GenerateGl" value="1" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Generate Payment Items</button>
        </div>
    @endif

    {{-- GL Items Table --}}
    @if(!empty($cart['gl_items']))
        <div class="bg-white shadow rounded-lg overflow-x-auto mb-4">
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-2 py-1">Account Code</th>
                        <th class="border px-2 py-1">Account Name</th>
                        <th class="border px-2 py-1">Debit</th>
                        <th class="border px-2 py-1">Credit</th>
                        <th class="border px-2 py-1">Memo</th>
                        <th class="border px-2 py-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $t_debit = 0; $t_credit = 0; @endphp
                    @foreach($cart['gl_items'] as $idx => $item)
                        @php
                            $acct = $accounts->firstWhere('code', $item['code_id']);
                            $debit = $item['amount'] > 0 ? $item['amount'] : 0;
                            $credit = $item['amount'] < 0 ? -$item['amount'] : 0;
                            $t_debit += $debit;
                            $t_credit += $credit;
                        @endphp
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="border px-2 py-1">{{ $acct->code ?? $item['code_id'] }}</td>
                            <td class="border px-2 py-1">{{ $acct->name ?? '' }}</td>
                            <td class="border px-2 py-1 text-right">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                            <td class="border px-2 py-1 text-right">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                            <td class="border px-2 py-1">{{ $item['memo'] }}</td>
                            <td class="border px-2 py-1 text-center">
                                <button type="submit" name="Edit{{ $idx }}" value="{{ $idx }}" class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                                <button type="submit" name="Delete" value="{{ $idx }}" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Delete this item?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-100">
                        <td class="border px-2 py-1" colspan="2">Total</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($t_debit, 2) }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($t_credit, 2) }}</td>
                        <td class="border px-2 py-1" colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Add GL Item Form --}}
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <table class="table-auto border-collapse">
                <tr>
                    <td class="p-1 text-right font-semibold">GL Account:</td>
                    <td class="p-1">
                        <select name="code_id" class="border px-2 py-1 min-w-[250px]">
                            <option value="">Select GL Account</option>
                            @foreach($accounts as $acct)
                                <option value="{{ $acct->code }}" @selected(isset($edit_index) && isset($cart['gl_items'][$edit_index]) && $cart['gl_items'][$edit_index]['code_id'] == $acct->code)>{{ $acct->code }} - {{ $acct->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Amount Debit:</td>
                    <td class="p-1"><input type="text" name="AmountDebit" value="{{ isset($edit_index) && isset($cart['gl_items'][$edit_index]) && $cart['gl_items'][$edit_index]['amount'] > 0 ? number_format($cart['gl_items'][$edit_index]['amount'], 2, '.', '') : '' }}" size="15" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Amount Credit:</td>
                    <td class="p-1"><input type="text" name="AmountCredit" value="{{ isset($edit_index) && isset($cart['gl_items'][$edit_index]) && $cart['gl_items'][$edit_index]['amount'] < 0 ? number_format(-$cart['gl_items'][$edit_index]['amount'], 2, '.', '') : '' }}" size="15" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Memo:</td>
                    <td class="p-1"><input type="text" name="LineMemo" value="{{ isset($edit_index) && isset($cart['gl_items'][$edit_index]) ? $cart['gl_items'][$edit_index]['memo'] : '' }}" size="30" class="border px-2 py-1"></td>
                </tr>
            </table>
            <div class="text-center mt-2">
                @if($edit_index !== null)
                    <button type="submit" name="UpdateItem" value="1" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded">Update</button>
                    <button type="submit" name="CancelItemChanges" value="1" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded">Cancel</button>
                @else
                    <button type="submit" name="AddItem" value="1" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Add</button>
                @endif
            </div>
        </div>

        {{-- Previous Advances Allocation Table --}}
        @if($advances->isNotEmpty())
            <div class="bg-white shadow rounded-lg mb-4 p-2">
                <h3 class="font-bold text-lg mb-2">Allocate Advances to this Payment</h3>
                <table class="table-auto w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-2 py-1">Trans #</th>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Amount</th>
                            <th class="border px-2 py-1">Remaining</th>
                            <th class="border px-2 py-1">Allocation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($advances as $adv)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="border px-2 py-1">{{ $adv->trans_no }}</td>
                                <td class="border px-2 py-1">{{ $adv->pay_date }}</td>
                                <td class="border px-2 py-1 text-right">{{ number_format($adv->pay_amount, 2) }}</td>
                                <td class="border px-2 py-1 text-right">{{ number_format($adv->remain, 2) }}</td>
                                <td class="border px-2 py-1 text-center">
                                    <input type="text" name="amount{{ $adv->id }}" value="{{ request('amount'.$adv->id, 0) }}" size="10" class="border px-1 py-0.5 text-right" onchange="document.getElementById('alloc_warn').style.display=(parseFloat(this.value)>0&&parseFloat(this.value)>{{ $adv->remain }})?'inline':'none'">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <span id="alloc_warn" style="display:none;color:red;">Warning: allocation exceeds remaining amount</span>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="text-center mt-4">
            <button type="submit" name="Process" value="1" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded font-bold">Process</button>
            <button type="submit" name="CancelOrder" value="1" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded font-bold">Cancel</button>
        </div>
    @endif
</form>
@endsection
