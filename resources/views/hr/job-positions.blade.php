@extends('layouts.app')
@section('title', 'Manage Job Positions')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Job Positions</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('hr.job-positions') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Id</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary amount</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay basis</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($positions as $p)
            <tr class="hover:bg-gray-50 {{ $p->inactive ? 'text-gray-400' : '' }}">
                <td class="px-4 py-2 text-sm">{{ $p->position_id }}</td>
                <td class="px-4 py-2 text-sm">{{ $p->position_name }}</td>
                <td class="px-4 py-2 text-sm">{{ number_format($p->pay_amount ?? 0, 2) }}</td>
                <td class="px-4 py-2 text-sm">{{ $p->pay_basis == 0 ? 'Monthly' : 'Daily' }}</td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="{{ $p->position_id }}"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $p->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $p->inactive ? 'Yes' : 'No' }}
                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $p->position_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete{{ $p->position_id }}" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this job position?')">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
        @if(!$show_inactive)
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="7" class="px-4 py-2 text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="show_inactive" value="1" onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Show also inactive</span>
                    </label>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
</form>

<form method="POST" action="{{ route('hr.job-positions') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id !== -1)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Position Name:</label>
            <input type="text" name="name" value="{{ old('name', $selected_position->position_name ?? '') }}" maxlength="50" size="37"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        @if(!$USE_DEPT_ACC)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary Basic Account:</label>
            <select name="AccountId"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">&nbsp;</option>
                @foreach($all_accounts as $acc)
                <option value="{{ $acc->code }}" {{ old('AccountId', $selected_position->pay_rule_id ?? '') == $acc->code ? 'selected' : '' }}>
                    {{ $acc->code }} - {{ $acc->name }} ({{ $acc->account_type }})
                </option>
                @endforeach
            </select>
        </div>
        @else
            <input type="hidden" name="AccountId" value="">
        @endif

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary Basic Amount:</label>
            <input type="text" name="amount" value="{{ old('amount', $selected_position->pay_amount ?? '') }}"
                class="w-40 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pay Basis:</label>
            <div class="mt-1">
                <label class="inline-flex items-center mr-4">
                    <input type="radio" name="payBasis" value="0" {{ old('payBasis', $selected_position->pay_basis ?? '0') == '0' ? 'checked' : '' }}
                        class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Monthly salary</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="payBasis" value="1" {{ old('payBasis', $selected_position->pay_basis ?? '0') == '1' ? 'checked' : '' }}
                        class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Daily wage</span>
                </label>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id !== -1)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('hr.job-positions') }}"
                class="ml-2 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">
                Cancel
            </a>
        @else
            <button type="submit" name="ADD_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Add New
            </button>
        @endif
    </div>
</form>
@endsection
