@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr('#from_date', { dateFormat: 'd/m/Y' });
flatpickr('#to_date', { dateFormat: 'd/m/Y' });
</script>
@endpush
@extends('layouts.app')
@section('title', 'Employees Attendance')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employees Attendance</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if(!$has_employee)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">There are no employees for attendance.</div>
@endif

<form method="post" action="{{ url()->current() }}">
    @csrf

    <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4">
        <tr>
            <td class="p-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1 text-right font-semibold">From:</td>
                        <td class="p-1"><input type="text" name="from_date" id="from_date" value="{{ $from_date }}" size="10" maxlength="10" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">To:</td>
                        <td class="p-1"><input type="text" name="to_date" id="to_date" value="{{ $to_date }}" size="10" maxlength="10" class="border px-2 py-1"></td>
                        <td class="p-1 text-right font-semibold">Department:</td>
                        <td class="p-1">
                            <select name="DeptId" class="border px-2 py-1" onchange="this.form.submit();">
                                <option value="">All departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}" @selected($dept_id == $dept->dept_id)>{{ $dept->dept_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1">
                            <input type="submit" name="bulk" value="Bulk" title="Record all as regular work" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($has_employee)
        <table class="table-auto border-collapse bg-white shadow rounded-lg w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">Employee</th>
                    <th class="border px-2 py-1">Regular time</th>
                    @foreach($overtimes as $ot)
                        <th class="border px-2 py-1">{{ $ot->overtime_name }}</th>
                    @endforeach
                    <th class="border px-2 py-1">Leave Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                    @php
                        $checked = request()->method() == 'GET' || request((string)$emp->id) == 1;
                        $reg_val = old($emp->id . '-0', request($emp->id . '-0', ''));
                        $leave_val = old($emp->id . '-leave', request($emp->id . '-leave', ''));
                    @endphp
                    <tr>
                        <td class="border px-2 py-1">
                            {{ $emp->id }}
                            <input type="checkbox" name="{{ $emp->id }}" value="1" {{ $checked ? 'checked' : '' }}>
                        </td>
                        <td class="border px-2 py-1">{{ $emp->name }}</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="{{ $emp->id }}-0" value="{{ $reg_val }}" size="10" maxlength="10" class="border px-2 py-1 text-right">
                        </td>
                        @foreach($overtimes as $ot)
                            @php
                                $ot_val = old($emp->id . '-' . $ot->overtime_id, request($emp->id . '-' . $ot->overtime_id, ''));
                            @endphp
                            <td class="border px-2 py-1">
                                <input type="text" name="{{ $emp->id }}-{{ $ot->overtime_id }}" value="{{ $ot_val }}" size="10" maxlength="10" class="border px-2 py-1 text-right">
                            </td>
                        @endforeach
                        <td class="border px-2 py-1">
                            <select name="{{ $emp->id }}-leave" class="border px-2 py-1">
                                <option value="">Select Leave Type</option>
                                @foreach($leave_types as $lt)
                                    <option value="{{ $lt->leave_id }}" @selected($leave_val == $lt->leave_id)>{{ $lt->leave_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <div class="text-center">
            <input type="submit" name="addatt" value="Save attendance" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        </div>
    @endif
</form>
@endsection
