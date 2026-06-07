@extends('layouts.app')
@section('title', 'Leave Types')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Leave Types</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('hr.leave-types') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Id</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary Rate (%)</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($leave_types as $lt)
            <tr class="hover:bg-gray-50 {{ $lt->inactive ? 'text-gray-400' : '' }}">
                <td class="px-4 py-2 text-sm">{{ $lt->leave_id }}</td>
                <td class="px-4 py-2 text-sm">{{ $lt->leave_name }}</td>
                <td class="px-4 py-2 text-sm">{{ $lt->leave_code }}</td>
                <td class="px-4 py-2 text-sm">{{ number_format($lt->pay_rate, 4) }}%</td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="{{ $lt->leave_id }}"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $lt->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $lt->inactive ? 'Yes' : 'No' }}
                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $lt->leave_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete{{ $lt->leave_id }}" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this leave type?')">Delete</button>
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

<form method="POST" action="{{ route('hr.leave-types') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id !== -1)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary rate:</label>
            <div class="flex items-center gap-2">
                <input type="text" name="pay_rate" value="{{ old('pay_rate', $selected_leave_type->pay_rate ?? '') }}" maxlength="20" size="20"
                    class="w-40 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="text-sm text-gray-500">%</span>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type Name:</label>
            <input type="text" name="leave_name" value="{{ old('leave_name', $selected_leave_type->leave_name ?? '') }}" maxlength="30" size="30"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type Code:</label>
            <input type="text" name="leave_code" value="{{ old('leave_code', $selected_leave_type->leave_code ?? '') }}" maxlength="3" size="5"
                class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id !== -1)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('hr.leave-types') }}"
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
