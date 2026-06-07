@extends('layouts.app')
@section('title', 'Employee Transaction Inquiry')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Transaction Inquiry</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $msg !!}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{!! $error !!}</div>
@endif

<form method="post" action="{{ route('hr.inquiries.transactions') }}">
    @csrf
    <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">Reference:</td>
                        <td class="p-1"><input type="text" name="Ref" value="{{ $ref }}" size="15" class="border px-2 py-1" placeholder="Enter reference fragment or leave empty"></td>
                        <td class="p-1 text-right font-semibold">Memo:</td>
                        <td class="p-1"><input type="text" name="Memo" value="{{ $memo }}" size="15" class="border px-2 py-1" placeholder="Enter memo fragment or leave empty"></td>
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" name="FromDate" value="{{ $from_date }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" name="ToDate" value="{{ $to_date }}" size="12" class="border px-2 py-1"></td>
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1">
                            <select name="DeptId" class="border px-2 py-1" onchange="this.form.submit();">
                                <option value="">All departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}" @selected($dept_id == $dept->dept_id)>{{ $dept->dept_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1">
                            <select name="EmpId" class="border px-2 py-1 min-w-[180px]">
                                <option value="">All employees</option>
                                @foreach($employees_filter as $emp)
                                    <option value="{{ $emp->id }}" @selected($emp_id == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="OnlyUnpaid" value="1" {{ $only_unpaid ? 'checked' : '' }} class="mr-1">
                                <span class="font-semibold">Only unpaid:</span>
                            </label>
                        </td>
                        <td class="p-1">
                            <input type="submit" name="Search" value="Search" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        </td>
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
    </table>
</form>

@if($has_searched)
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="table-auto border-collapse w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Date</th>
                    <th class="border px-2 py-1">Trans #</th>
                    <th class="border px-2 py-1">Type</th>
                    <th class="border px-2 py-1">Employee ID</th>
                    <th class="border px-2 py-1">Employee Name</th>
                    <th class="border px-2 py-1">Payslip No</th>
                    <th class="border px-2 py-1">Pay from</th>
                    <th class="border px-2 py-1">Pay to</th>
                    <th class="border px-2 py-1 text-right">Amount</th>
                    <th class="border px-2 py-1 text-center"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $row)
                    @php
                        $is_payslip = $row->type == 0;
                        $is_advance = $row->type == 1 && ($row->payslip_no == 0 || $row->payslip_no === null);
                        $is_advice = $row->type == 1 && $row->payslip_no != 0;

                        if ($is_payslip) $type_label = 'Payslip';
                        elseif ($is_advance) $type_label = 'Employee advance';
                        else $type_label = 'Payment advice';
                    @endphp
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border px-2 py-1">{{ $row->trans_date }}</td>
                        <td class="border px-2 py-1">
                            @if($row->trans_no != 0)
                                <a href="{{ route('hr.payslips', ['AddedID' => $row->trans_no]) }}" class="text-blue-600 hover:text-blue-800">{{ $row->trans_no }}</a>
                            @endif
                        </td>
                        <td class="border px-2 py-1">{{ $type_label }}</td>
                        <td class="border px-2 py-1">{{ $row->emp_id }}</td>
                        <td class="border px-2 py-1">{{ $row->emp_name }}</td>
                        <td class="border px-2 py-1">{{ $row->payslip_no ?: '' }}</td>
                        <td class="border px-2 py-1">{{ $row->from_date ?? '' }}</td>
                        <td class="border px-2 py-1">{{ $row->to_date ?? '' }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($row->amount, 2) }}</td>
                        <td class="border px-2 py-1 text-center">
                            @if($is_advice)
                                <a href="{{ route('hr.payslips', ['AddedID' => $row->trans_no]) }}" class="text-blue-600 hover:text-blue-800 text-xs" title="Print this Payslip">Print</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="border px-2 py-1 text-center" colspan="10">No records</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection
