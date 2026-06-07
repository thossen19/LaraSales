@extends('layouts.app')
@section('title', 'Payroll default Settings')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Payroll default Settings</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('hr.default-settings') }}" class="bg-white shadow rounded-lg">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
        <!-- Left column: General GL -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">General GL</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payroll payable account:</label>
                <select name="payroll_payable_act"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">&nbsp;</option>
                    @foreach($all_accounts as $acc)
                    <option value="{{ $acc->code }}" {{ $prefs['payroll_payable_act'] == $acc->code ? 'selected' : '' }}>
                        {{ $acc->code }} - {{ $acc->name }} ({{ $acc->account_type }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deductible account:</label>
                <select name="payroll_deductleave_act"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Use Salary Basic Account</option>
                    @foreach($all_accounts as $acc)
                    <option value="{{ $acc->code }}" {{ $prefs['payroll_deductleave_act'] == $acc->code ? 'selected' : '' }}>
                        {{ $acc->code }} - {{ $acc->name }} ({{ $acc->account_type }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Overtime account:</label>
                <select name="payroll_overtime_act"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Use Salary Basic Account</option>
                    @foreach($all_accounts as $acc)
                    <option value="{{ $acc->code }}" {{ $prefs['payroll_overtime_act'] == $acc->code ? 'selected' : '' }}>
                        {{ $acc->code }} - {{ $acc->name }} ({{ $acc->account_type }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Right column: Working time parameters + Others -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Working time parameters</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Work days per month:</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="payroll_month_work_days" value="{{ old('payroll_month_work_days', $prefs['payroll_month_work_days']) }}" maxlength="6" size="6"
                        class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm text-gray-500">days</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Work hours per day:</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="payroll_work_hours" value="{{ old('payroll_work_hours', $prefs['payroll_work_hours']) }}" maxlength="6" size="6"
                        class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm text-gray-500">hours</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Weekend:</label>
                <select name="weekend_day"
                    class="w-full max-w-xs border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($weekdays as $val => $label)
                    <option value="{{ $val }}" {{ $prefs['weekend_day'] == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 mt-6">Others</h3>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="payroll_dept_based" value="1" {{ $prefs['payroll_dept_based'] == '1' ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Salary based on department:</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Number of Grades:</label>
                <select name="payroll_grades"
                    class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @for($i = 1; $i <= $max_grade_number; $i++)
                    <option value="{{ $i }}" {{ $prefs['payroll_grades'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200 text-center">
        <button type="submit" name="submit" value="1"
            class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            Update
        </button>
    </div>
</form>
@endsection
