@extends('layouts.app')
@section('title', 'Journal Inquiry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Journal Inquiry</h2>
</div>

<form method="POST" action="{{ route('banking.inquiries.journal') }}">
@csrf

<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
        <td class="py-1 pr-4">
            <input type="text" name="Ref" value="{{ $ref }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Type:</td>
        <td class="py-1 pr-4">
            <select name="filterType" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                @foreach($journal_types as $val => $label)
                    <option value="{{ $val }}" {{ $filterType == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
        <td class="py-1 pr-4">
            <input type="date" name="FromDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">To:</td>
        <td class="py-1">
            <input type="date" name="ToDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
    </tr>
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Memo:</td>
        <td class="py-1 pr-4">
            <input type="text" name="Memo" value="{{ $memo }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">User:</td>
        <td class="py-1 pr-4">
            <select name="userid" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </td>
        @if($use_dimension)
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension:</td>
        <td class="py-1 pr-4">
            <select name="dimension" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All --</option>
                @foreach($dimensions as $d)
                    <option value="{{ $d->id }}" {{ $dimension == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </td>
        @endif
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="AlsoClosed" value="1" {{ $alsoClosed ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">Show closed:</span>
            </label>
        </td>
        <td class="py-1">
            <button type="submit" name="Search" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Search</button>
        </td>
    </tr>
</table>
</form>

@if(request('Search') || $entries->count() > 0)
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Trans #</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Counterparty</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">View</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($entries as $e)
                    @php
                        $typeLabels = ['journal' => 'Journal Entry', 'deposit' => 'Bank Deposit', 'payment' => 'Bank Payment', 'transfer' => 'Bank Transfer', 'accrual' => 'Accrual'];
                        $typeLabel = $typeLabels[$e->reference_type] ?? ucfirst($e->reference_type);
                        $amount = $e->total_debit;
                        $viewUrl = route('banking.inquiries.journal'); // placeholder view link
                        $editUrl = '#'; // placeholder edit link
                        $isEditable = $e->is_posted ? false : true;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-center text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $e->entry_date }}</td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $typeLabel }}</td>
                        <td class="px-3 py-2 text-sm"><a href="#" class="text-indigo-600 hover:text-indigo-900">{{ $e->id }}</a></td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $e->reference_type }}</td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $e->entry_number }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($amount, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600 max-w-xs truncate">{{ $e->description }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $e->user_name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 text-center">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm" title="View GL">GL</a>
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($isEditable)
                                <a href="{{ $editUrl }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                            @else
                                <span class="text-gray-400 text-sm">--</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-3 py-8 text-center text-gray-500">No journal entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $entries->links() }}
        </div>
    @endif
</div>
@endif
@endsection