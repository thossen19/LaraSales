@extends('layouts.app')
@section('title', 'Manage Department')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Department</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('hr.departments') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Id</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department Name</th>
                @if($USE_DEPT_ACC)
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary Basic Account</th>
                @endif
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($departments as $d)
            <tr class="hover:bg-gray-50 {{ $d->inactive ? 'text-gray-400' : '' }}">
                <td class="px-4 py-2 text-sm">{{ $d->dept_id }}</td>
                <td class="px-4 py-2 text-sm">{{ $d->dept_name }}</td>
                @if($USE_DEPT_ACC)
                <td class="px-4 py-2 text-sm">{{ $d->basic_account }}</td>
                @endif
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="{{ $d->dept_id }}"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $d->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $d->inactive ? 'Yes' : 'No' }}
                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $d->dept_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete{{ $d->dept_id }}" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this department?')">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
        @if(!$show_inactive)
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="{{ $USE_DEPT_ACC ? 6 : 5 }}" class="px-4 py-2 text-sm">
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

<form method="POST" action="{{ route('hr.departments') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Department Name:</label>
            <input type="text" name="name" value="{{ old('name', $selected_department->dept_name ?? '') }}" maxlength="60"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        @if($USE_DEPT_ACC)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Salary Basic Account:</label>
            <select name="basic_acc"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select basic account</option>
                @foreach($all_accounts as $acc)
                <option value="{{ $acc->code }}" {{ old('basic_acc', $selected_department->basic_account ?? '') == $acc->code ? 'selected' : '' }}>
                    {{ $acc->code }} - {{ $acc->name }} ({{ $acc->account_type }})
                </option>
                @endforeach
            </select>
        </div>
        @else
            <input type="hidden" name="basic_acc" value="">
        @endif
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('hr.departments') }}"
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
