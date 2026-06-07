@extends('layouts.app')
@section('title', 'Search Dimensions - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Search Dimensions</h2>
</div>

<form method="POST" action="{{ route('dimensions.inquiries.index') }}">
@csrf
<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
        <td class="py-1 pr-4">
            <input type="text" name="OrderNumber" value="{{ $ref }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Type:</td>
        <td class="py-1 pr-4">
            <select name="type_" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">All</option>
                @for($i = 1; $i <= ($use_dimension ?: 1); $i++)
                    <option value="{{ $i }}" {{ $type_ !== '' && (int)$type_ == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
        <td class="py-1 pr-4">
            <input type="date" name="FromDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">To:</td>
        <td class="py-1 pr-4">
            <input type="date" name="ToDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="OverdueOnly" value="1" {{ $overdueOnly ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">Only Overdue:</span>
            </label>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="OpenOnly" value="1" {{ $openOnly ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">Only Open:</span>
            </label>
        </td>
        <td class="py-1">
            <button type="submit" name="SearchOrders" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Search</button>
        </td>
    </tr>
</table>
</form>

@if($search || count($dimensions) > 0)
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Closed</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Edit</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Print</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($dimensions as $d)
                    @php $isOverdue = $d->is_overdue; @endphp
                    <tr class="hover:bg-gray-50 {{ $isOverdue ? 'bg-red-50' : '' }}" title="{{ $isOverdue ? 'Marked dimensions are overdue.' : '' }}">
                        <td class="px-3 py-2 text-sm text-center"><a href="{{ route('dimensions.entry', ['selected_id' => $d->id]) }}" class="text-indigo-600 hover:text-indigo-900">{{ $d->id }}</a></td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $d->reference }}</td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $d->name }}</td>
                        <td class="px-3 py-2 text-sm text-center text-gray-600">{{ $d->type_ }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $d->date_ }}</td>
                        <td class="px-3 py-2 text-sm text-gray-900">{{ $d->due_date }}</td>
                        <td class="px-3 py-2 text-sm text-center">{{ $d->closed ? 'Yes' : 'No' }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($d->balance, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-center"><a href="{{ route('dimensions.entry', ['trans_no' => $d->id]) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a></td>
                        <td class="px-3 py-2 text-sm text-center"><a href="#" class="text-indigo-600 hover:text-indigo-900" title="Print">Print</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-3 py-8 text-center text-gray-500">No dimensions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection