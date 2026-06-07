@extends('layouts.app')
@section('title', 'Manage Grades')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Grades</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if($position_count == 0)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">Please define Job Positions First</div>
@endif

<form method="POST" action="{{ route('hr.grades') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job Position</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic Amount</th>
                @for($i = 1; $i <= $grades_no; $i++)
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade {{ $i }}</th>
                @endfor
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($positions as $p)
            <tr class="hover:bg-gray-50 {{ $p->inactive ? 'text-gray-400' : '' }}">
                <td class="px-4 py-2 text-sm">{{ $p->position_name }}</td>
                <td class="px-4 py-2 text-sm text-right">{{ number_format($p->pay_amount ?? 0, 2) }}</td>
                @for($i = 1; $i <= $grades_no; $i++)
                <td class="px-4 py-2 text-sm text-right">
                    {{ isset($grade_amounts[$p->position_id][$i]) ? number_format($grade_amounts[$p->position_id][$i], 2) : '' }}
                </td>
                @endfor
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $p->position_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Delete{{ $p->position_id }}" value="1"
                        class="text-red-600 hover:text-red-900 text-sm"
                        onclick="return confirm('Are you sure you want to delete grade table for this job position?')">Delete</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 2 + $grades_no + 2 }}" class="px-4 py-8 text-center text-gray-500">No job positions defined yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</form>

<form method="POST" action="{{ route('hr.grades') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id !== -1)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Position:</label>
            <select name="position_id"
                class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select Job Position</option>
                @foreach($all_positions as $pos)
                <option value="{{ $pos->position_id }}"
                    {{ old('position_id', $selected_id !== -1 ? $selected_id : '') == $pos->position_id ? 'selected' : '' }}>
                    {{ $pos->position_name }}
                </option>
                @endforeach
            </select>
        </div>

        @if($selected_id !== -1 && $selected_position)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Amount:</label>
            <span class="text-sm text-gray-900 font-medium">{{ number_format($selected_position->pay_amount ?? 0, 2) }}</span>
        </div>
        @endif

        @for($i = 1; $i <= $grades_no; $i++)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Grade {{ $i }}:</label>
            <input type="text" name="amt_{{ $i }}"
                value="{{ old('amt_'.$i, isset($selected_position_grades[$i]) ? (empty($selected_position_grades[$i]) ? number_format($selected_position->pay_amount ?? 0, 2) : number_format($selected_position_grades[$i], 2)) : '') }}"
                class="w-40 border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        @endfor
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id !== -1)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('hr.grades') }}"
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
