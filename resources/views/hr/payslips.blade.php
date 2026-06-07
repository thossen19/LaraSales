@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#date_', { dateFormat: 'd/m/Y' });
flatpickr('#from_date', { dateFormat: 'd/m/Y' });
flatpickr('#to_date', { dateFormat: 'd/m/Y' });
</script>
@endpush
@extends('layouts.app')
@section('title', 'Employee Payslip Entry')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Payslip Entry</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $msg !!}</div>
    @if($added_trans_no)
        <div class="text-center mb-4">
            <a href="{{ route('hr.payslips', ['NewPayslip' => 'Yes']) }}" class="text-blue-600 hover:text-blue-800 underline mx-2">Enter &amp;New Payslip</a>
            <a href="{{ route('hr.payment-advice') }}?PayslipNo={{ $added_payslip_no }}" class="text-blue-600 hover:text-blue-800 underline mx-2">Make Payment &amp;Advice for this Payslip</a>
        </div>
    @endif
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{!! $error !!}</div>
@endif

<form method="post" action="{{ route('hr.payslips') }}">
    @csrf

    <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Date:</td>
                        <td class="p-1"><input type="text" id="date_" name="date_" value="{{ old('date_', !empty($cart['tran_date']) ? date('d/m/Y', strtotime($cart['tran_date'])) : date('d/m/Y')) }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Reference:</td>
                        <td class="p-1"><input type="text" name="ref" value="{{ old('ref', $cart['reference'] ?? '') }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" id="from_date" name="from_date" value="{{ old('from_date', isset($cart['from_date']) && $cart['from_date'] ? date('d/m/Y', strtotime($cart['from_date'])) : '') }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" id="to_date" name="to_date" value="{{ old('to_date', isset($cart['to_date']) && $cart['to_date'] ? date('d/m/Y', strtotime($cart['to_date'])) : '') }}" size="12" class="border px-2 py-1"></td>
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
                                <option value="">Select employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected(old('person_id', $cart['person_id'] ?? '') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1 text-right font-semibold">Pay Basis:</td>
                        <td class="p-1">{{ $pay_basis_label ?: '' }}</td>
                        <td class="p-1 text-right font-semibold">Payslip No:</td>
                        <td class="p-1">{{ $next_payslip_no }}</td>
                        <input type="hidden" name="PaySlipNo" value="{{ $next_payslip_no }}">
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Work days:</td>
                        <td class="p-1">{{ $info['work_days'] ?? '' }} days</td>
                        <td class="p-1 text-right font-semibold">Leave hours:</td>
                        <td class="p-1">{{ isset($info['leave_hours']) ? number_format($info['leave_hours'], 2) : '' }} hours</td>
                        @if(isset($info['ot_totals']))
                            @foreach($overtimes as $ot)
                                <td class="p-1 text-right font-semibold">{{ $ot->overtime_name }}:</td>
                                <td class="p-1">{{ number_format($info['ot_totals'][$ot->overtime_id] ?? 0, 2) }} hours</td>
                            @endforeach
                        @else
                            @foreach($overtimes as $ot)
                                <td class="p-1 text-right font-semibold">{{ $ot->overtime_name }}:</td>
                                <td class="p-1"></td>
                            @endforeach
                        @endif
                    </tr>
                </table>
                <input type="hidden" name="leaves" value="{{ $info['leave_hours'] ?? 0 }}">
                <input type="hidden" name="deductableleaves" value="{{ $cart['deductable_leaves'] ?? 0 }}">
                <input type="hidden" name="workdays" value="{{ $info['work_days'] ?? 0 }}">
            </td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        @php $cols = 3; @endphp
                        @forelse($leave_types as $lt)
                            <td class="p-1 text-right font-semibold">{{ $lt->leave_name }}:</td>
                            <td class="p-1">{{ isset($info['leave_counts'][$lt->leave_id]) ? $info['leave_counts'][$lt->leave_id] . ' day(s)' : '' }}</td>
                            @php if ($loop->iteration % $cols == 0) echo '</tr><tr>'; @endphp
                        @empty
                            <td class="p-1"></td>
                        @endforelse
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if(count($cart['gl_items']) == 0)
        <div class="text-center mb-4">
            <input type="submit" name="GeneratePayslip" value="Generate Payslip" title="Generate Payslip For Process" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        </div>
    @endif

    @if(count($cart['gl_items']) > 0)
        <div id="payslip_trans">
            <div class="bg-white shadow rounded-lg p-4 mb-4">
                <h3 class="font-bold text-lg mb-2">Rows</h3>
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
                                                <option value="{{ $acc->code }}" @selected($item['code_id'] == $acc->code)>{{ $acc->code }} - {{ $acc->name }}</option>
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
                                        <button type="submit" name="Delete" value="{{ $line }}" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Remove line from journal?')">Delete</button>
                                    </td>
                                </tr>
                                @php $k++; @endphp
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-100">
                            <td colspan="2" class="border px-2 py-1 text-right">Total Salary</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($total_debit, 2) }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($total_credit, 2) }}</td>
                            <td class="border px-2 py-1" colspan="2"></td>
                        </tr>
                        @if($edit_index === null || !isset($cart['gl_items'][$edit_index]))
                        <tr>
                            <td class="border px-2 py-1">
                                <select name="code_id" class="border px-2 py-1 text-xs">
                                    <option value="">Select GL Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->code }}" @selected(old('code_id') == $acc->code)>{{ $acc->code }} - {{ $acc->name }}</option>
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

            <div class="text-center mb-4">
                <textarea name="memo_" rows="3" cols="50" class="border px-2 py-1" placeholder="Memo">{{ old('memo_', $cart['memo_'] ?? '') }}</textarea>
            </div>

            <div class="text-center space-x-2">
                <input type="submit" name="Process" value="Process PaySlip" title="Process journal entry only if debits equal to credits" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                <input type="submit" name="CancelOrder" value="Cancel" title="Cancels document entry or removes Gl items" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            </div>
        </div>
    @endif
</form>
@endsection