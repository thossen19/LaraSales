@extends('layouts.app')
@section('title', 'View or Print Transactions - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">View or Print Transactions</h2>
    <p class="mt-2 text-gray-600">View and print transaction records.</p>
</div>

@if($error_msg)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error_msg }}</div>
@endif

<div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4 text-sm">Only documents can be printed.</div>

<form method="GET" action="{{ route('setup.view-print-transactions') }}">
    <table class="mb-4">
        <tr>
            <td class="pr-3 text-sm text-gray-700 font-medium">Type:</td>
            <td class="pr-3">
                <select name="filterType" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @foreach($systypes as $code => $label)
                        @if(!in_array($code, [50, 51]))
                            <option value="{{ $code }}" {{ $filterType == $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td class="pr-3 text-sm text-gray-700 font-medium">from #:</td>
            <td class="pr-3">
                <input type="text" name="FromTransNo" value="{{ $fromNo }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </td>
            <td class="pr-3 text-sm text-gray-700 font-medium">to #:</td>
            <td class="pr-3">
                <input type="text" name="ToTransNo" value="{{ $toNo }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </td>
            <td>
                <button type="submit" name="ProcessSearch" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Search</button>
            </td>
        </tr>
    </table>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Date</th>
                @if($printable)
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Print</th>
                @endif
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">GL</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($results as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-indigo-600 whitespace-nowrap">
                        <a href="#" class="hover:underline" title="View transaction">{{ $r['trans_no'] }}</a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $r['ref'] ?? '' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $r['trans_date'] }}</td>
                    @if($printable)
                        <td class="px-4 py-3 text-center">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm" title="Print document">Print</a>
                        </td>
                    @endif
                    <td class="px-4 py-3 text-center">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm" title="View GL entries">GL</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $printable ? 5 : 4 }}" class="px-4 py-8 text-center text-gray-500">
                        @if(request()->has('ProcessSearch'))
                            No transactions found.
                        @else
                            Select transaction type and press Search button.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection