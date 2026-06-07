@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#FromDate', { dateFormat: 'Y-m-d' });
flatpickr('#ToDate', { dateFormat: 'Y-m-d' });
</script>
@endpush
@extends('layouts.app')
@section('title', 'Timesheet Inquiry')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Timesheet Inquiry</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $msg !!}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{!! $error !!}</div>
@endif

<form method="post" action="{{ route('hr.timesheet') }}">
    @csrf
    <table class="table-auto border-collapse bg-white shadow rounded-lg w-full mb-4">
        <tr>
            <td class="border px-3 py-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1">
                            <select name="DeptId" class="border px-2 py-1" onchange="document.forms[0].submit();">
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
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" id="FromDate" name="FromDate" value="{{ $from_date }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" id="ToDate" name="ToDate" value="{{ $to_date }}" size="12" class="border px-2 py-1"></td>
                        <td class="p-1">
                            <select name="OvertimeId" class="border px-2 py-1">
                                <option value="">Regular time</option>
                                @foreach($overtimes as $ot)
                                    <option value="{{ $ot->overtime_id }}" @selected($ot_id !== '' && $ot_id == $ot->overtime_id)>{{ $ot->overtime_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1">
                            <input type="submit" name="Search" value="Search" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

@if($search && count($day_columns) > 0)
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="table-auto border-collapse w-full text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1 text-center">Id</th>
                    <th class="border px-2 py-1">Employee Name</th>
                    @foreach($day_columns as $col)
                        @if($col['is_weekend'])
                            <th class="border px-2 py-1 text-center" style="background:#FFCCCC;">{{ $col['day'] }}<p hidden>{{ $col['month'] }}</p></th>
                        @else
                            <th class="border px-2 py-1 text-center">{{ $col['day'] }}<p hidden>{{ $col['month'] }}</p></th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="border px-2 py-1 text-center">{{ $emp->id }}</td>
                        <td class="border px-2 py-1">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                        @foreach($day_columns as $col)
                            @php
                                $display = '';
                                $bg = '';
                                $att = $att_data[$emp->id][$col['date']] ?? null;
                                $lev = $lev_data[$emp->id][$col['date']] ?? null;

                                if ($att) {
                                    $display = $att->hours;
                                } elseif ($lev) {
                                    $code = $lev->leave_code;
                                    if ($lev->leave_pay_rate >= 100)
                                        $display = '<b style="color:green">' . $code . '</b>';
                                    elseif ($lev->leave_pay_rate > 0)
                                        $display = '<b style="color:orange">' . $code . '</b>';
                                    else
                                        $display = '<b style="color:red">' . $code . '</b>';
                                }
                            @endphp
                            @if($col['is_weekend'])
                                <td class="border px-2 py-1 text-center" style="background:#FFCCCC;">{!! $display !!}</td>
                            @else
                                <td class="border px-2 py-1 text-center">{!! $display !!}</td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="border px-2 py-1 text-center" colspan="{{ count($day_columns) + 2 }}">No records</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($employees, 'links'))
        <div class="mt-4">
            {{ $employees->appends(request()->except('_token'))->links() }}
        </div>
    @endif
@endif
@endsection
