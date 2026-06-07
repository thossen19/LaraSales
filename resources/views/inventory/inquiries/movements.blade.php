@extends('layouts.app')
@section('title', 'Inventory Item Movement')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Inventory Item Movement</h2>
</div>

<form method="POST" action="{{ route('inventory.inquiries.movements') }}">
@csrf

<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Item:</td>
        <td class="py-1 pr-4">
            <select name="stock_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- Select --</option>
                @foreach($items as $it)
                    <option value="{{ $it->code }}" {{ $stock_id == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
                @endforeach
            </select>
        </td>
    </tr>
</table>

<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From Location:</td>
        <td class="py-1 pr-4">
            <select name="StockLocation" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">-- All --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->loc_code }}" {{ $location == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                @endforeach
            </select>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
        <td class="py-1 pr-4">
            <input type="date" name="AfterDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">To:</td>
        <td class="py-1 pr-4">
            <input type="date" name="BeforeDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1">
            <button type="submit" name="ShowMoves" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Show Movements</button>
        </td>
    </tr>
</table>
</form>

@if($stock_id)
<div id="doc_tbl">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Reference</th>
                @if($displayLocation)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                @endif
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity In</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity Out</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity On Hand</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @php
                $dec = 4;
                $headerSpan = $displayLocation ? 6 : 5;
                $colSpan = $displayLocation ? 9 : 8;
            @endphp
            <tr class="bg-blue-50">
                <td class="px-4 py-3 text-sm font-bold text-gray-900" colspan="{{ $headerSpan }}">Quantity on hand before {{ $toDate }}</td>
                <td class="px-4 py-3 text-sm text-gray-700" colspan="2">&nbsp;</td>
                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($beforeQty, $dec) }}</td>
            </tr>
            @php $j = 1; $k = 0; $runningQty = $beforeQty; @endphp
            @forelse($movements as $m)
                @php
                    $runningQty += $m->qty;
                    $typeName = $systypes[$m->trans_type] ?? 'Unknown (' . $m->trans_type . ')';
                    $qtyIn = $m->qty > 0 ? number_format($m->qty, $dec) : '';
                    $qtyOut = $m->qty < 0 ? number_format(-$m->qty, $dec) : '';
                @endphp
                <tr class="{{ $k % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $typeName }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $m->trans_no }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">---</td>
                    @if($displayLocation)
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $m->loc_code }}</td>
                    @endif
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $m->tran_date }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $m->memo }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $qtyIn }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $qtyOut }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($runningQty, $dec) }}</td>
                </tr>
                @if($j % 12 == 0)
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Reference</th>
                        @if($displayLocation)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity In</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity Out</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity On Hand</th>
                    </tr>
                @endif
                @php $j++; $k++; @endphp
            @empty
                <tr>
                    <td colspan="{{ $colSpan }}" class="px-4 py-6 text-center text-gray-500">No movements found for the selected criteria.</td>
                </tr>
            @endforelse
            <tr class="bg-blue-50">
                <td class="px-4 py-3 text-sm font-bold text-gray-900" colspan="{{ $headerSpan }}">Quantity on hand after {{ $fromDate }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-700 font-bold">{{ number_format($totalIn, $dec) }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-700 font-bold">{{ number_format($totalOut, $dec) }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-700 font-bold">{{ number_format($runningQty, $dec) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif
@endsection