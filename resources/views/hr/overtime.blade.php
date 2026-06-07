@extends('layouts.app')
@section('title', 'Manage Overtime')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Overtime</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('hr.overtime') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Id</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Overtime Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Overtime Rate</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($rates as $r)
            <tr class="hover:bg-gray-50 {{ $r->inactive ? 'text-gray-400' : '' }}">
                <td class="px-4 py-2 text-sm">{{ $r->overtime_id }}</td>
                <td class="px-4 py-2 text-sm">{{ $r->overtime_name }}</td>
                <td class="px-4 py-2 text-sm">{{ number_format($r->overtime_rate, 4) }}</td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="{{ $r->overtime_id }}"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $r->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $r->inactive ? 'Yes' : 'No' }}
                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $r->overtime_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete{{ $r->overtime_id }}" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete this overtime?')">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
        @if(!$show_inactive)
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="6" class="px-4 py-2 text-sm">
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

<form method="POST" action="{{ route('hr.overtime') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Overtime rate:</label>
            <input type="text" name="rate" value="{{ old('rate', $selected_rate->overtime_rate ?? '') }}" maxlength="20" size="20"
                class="w-40 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Overtime Name:</label>
            <input type="text" name="name" value="{{ old('name', $selected_rate->overtime_name ?? '') }}" maxlength="50" size="40"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('hr.overtime') }}"
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
