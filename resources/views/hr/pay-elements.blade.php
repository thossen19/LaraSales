@extends('layouts.app')
@section('title', 'Manage Pay Elements')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Pay Elements</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('hr.pay-elements') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Element</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($elements as $e)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm">{{ $e->element_name }}</td>
                <td class="px-4 py-2 text-sm text-center">{{ $e->account_code }}</td>
                <td class="px-4 py-2 text-sm">{{ $e->account_name }}</td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $e->element_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete{{ $e->element_id }}" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this pay element?')">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>

<form method="POST" action="{{ route('hr.pay-elements') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Element Name:</label>
            <input type="text" name="element_name" value="{{ old('element_name', $selected_element->element_name ?? '') }}" maxlength="50" size="37"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        @if($selected_id && $selected_element)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Account:</label>
            <span class="text-sm text-gray-900 font-medium">{{ $selected_element->account_code }}&nbsp;&nbsp;{{ $selected_element->account_name }}</span>
            <input type="hidden" name="AccountId" value="{{ $selected_element->account_code }}">
        </div>
        @else
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Account:</label>
            <select name="AccountId"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">&nbsp;</option>
                @foreach($all_accounts as $acc)
                <option value="{{ $acc->code }}" {{ old('AccountId') == $acc->code ? 'selected' : '' }}>
                    {{ $acc->code }} - {{ $acc->name }} ({{ $acc->account_type }})
                </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('hr.pay-elements') }}"
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
