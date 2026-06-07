@extends('layouts.app')
@section('title', 'Manage Payroll Rule')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Manage Payroll Rule</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

@if($position_count > 0)
<form method="POST" action="{{ route('hr.pay-elements-allocation') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center gap-4">
        <label class="block text-sm font-medium text-gray-700">Job Position:</label>
        <select name="PositionId" onchange="this.form.submit()"
            class="w-full max-w-md border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Select Job Position</option>
            @foreach($positions as $p)
            <option value="{{ $p->position_id }}" {{ $position_id == $p->position_id ? 'selected' : '' }}>
                {{ $p->position_name }}
            </option>
            @endforeach
        </select>
        <label class="inline-flex items-center">
            <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">Show inactive:</span>
        </label>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay Element</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($rules as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm">{{ $r->element_name }}</td>
                <td class="px-4 py-2 text-sm">{{ $r->account_code }} - {{ $r->account_name }}</td>
                <td class="px-4 py-2 text-sm text-center">
                    <input type="checkbox" name="Payroll{{ $r->account_code }}" value="1"
                        {{ in_array($r->account_code, $existing_rules) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No pay elements defined yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center flex items-center justify-center gap-4">
        @if($has_rules)
            <button type="submit" name="submit" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <button type="submit" name="delete" value="1"
                class="px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50 transition duration-150"
                onclick="return confirm('Delete payroll rules?')">
                Delete
            </button>
        @else
            <button type="submit" name="submit" value="1"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Save
            </button>
        @endif
    </div>
</form>
@else
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">Define Job Positions first.</div>
@endif
@endsection
