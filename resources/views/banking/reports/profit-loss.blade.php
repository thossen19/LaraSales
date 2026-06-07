@extends('layouts.app')
@section('title', 'Profit & Loss Drilldown - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Profit & Loss Drilldown</h2>
</div>

<form method="POST" action="{{ route('banking.reports.profit-loss') }}">
@csrf
<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransFromDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">To:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransToDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Compare to:</td>
        <td class="py-1 pr-4">
            <select name="Compare" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                @foreach($compareTypes as $idx => $label)
                    <option value="{{ $idx }}" {{ $compare == $idx ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </td>
        @if($use_dimension >= 1)
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 1:</td>
        <td class="py-1 pr-4">
            <select name="Dimension" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="0"> </option>
                @foreach($dimensions as $d)
                    <option value="{{ $d->id }}" {{ $dimension == $d->id ? 'selected' : '' }}>{{ $d->code ? $d->code . ' - ' : '' }}{{ $d->name }}</option>
                @endforeach
            </select>
        </td>
        @endif
        @if($use_dimension > 1)
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 2:</td>
        <td class="py-1 pr-4">
            <select name="Dimension2" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="0"> </option>
                @foreach($dimensions as $d)
                    <option value="{{ $d->id }}" {{ $dimension2 == $d->id ? 'selected' : '' }}>{{ $d->code ? $d->code . ' - ' : '' }}{{ $d->name }}</option>
                @endforeach
            </select>
        </td>
        @endif
        <td class="py-1">
            <button type="submit" name="Show" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Show</button>
        </td>
    </tr>
</table>
<input type="hidden" name="AccGrp" value="{{ $accGrp }}">
</form>

@if($show)
<div id="pl_tbl">
<div class="bg-white shadow rounded-lg overflow-hidden" style="max-width: 800px;">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Group/Account Name</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ $compareTypes[$compare] }}</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Achieved %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @if(!$drilldown)
                    {{-- Root Level --}}
                    @foreach($classData as $cd)
                        @php $class = $cd['class']; @endphp
                        <tr class="bg-indigo-50 font-bold">
                            <td class="px-4 py-2 text-sm font-bold text-gray-800" colspan="4">{{ $class->class_name }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Group/Account Name</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Period</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ $compareTypes[$compare] }}</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Achieved %</th>
                        </tr>
                        @foreach($cd['types'] as $td)
                            @if($td['per'] != 0 || $td['acc'] != 0)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm"><a href="{{ route('banking.reports.profit-loss') }}?TransFromDate={{ $fromDate }}&TransToDate={{ $toDate }}&Compare={{ $compare }}&Dimension={{ $dimension }}&Dimension2={{ $dimension2 }}&AccGrp={{ $td['type']->id }}" class="text-indigo-600 hover:text-indigo-900">{{ $td['type']->id }} {{ $td['type']->name }}</a></td>
                                <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($td['displayPer'], 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($td['displayAcc'], 2) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($td['pct'], 1) }}%</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr class="bg-yellow-50 font-bold">
                            <td class="px-4 py-2 text-sm font-bold text-gray-700">Total {{ $class->class_name }}</td>
                            <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($cd['displayPer'], 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($cd['displayAcc'], 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($cd['pct'], 1) }}%</td>
                        </tr>
                    @endforeach

                    {{-- Calculated Return (Net Income) --}}
                    <tr class="bg-yellow-50 font-bold">
                        <td class="px-4 py-2 text-sm font-bold text-gray-700">Calculated Return</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format(-$totalPer, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format(-$totalAcc, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($achieve(-$totalPer, -$totalAcc), 1) }}%</td>
                    </tr>
                @else
                    {{-- Drilldown Level --}}
                    @php
                        $accType = \DB::table('chart_types')->where('id', $accGrp)->first();
                    @endphp
                    <tr class="bg-indigo-50 font-bold">
                        <td class="px-4 py-2 text-sm font-bold text-gray-800" colspan="4">{{ $accGrp }} {{ $accType ? $accType->name : '' }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Group/Account Name</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ $compareTypes[$compare] }}</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Achieved %</th>
                    </tr>

                    {{-- Accounts directly under this type --}}
                    @foreach($drilldownAccounts as $da)
                    <tr class="bg-gray-50 hover:bg-gray-100">
                        <td class="px-4 py-2 text-sm"><a href="{{ route('banking.inquiries.gl') }}?TransFromDate={{ $fromDate }}&TransToDate={{ $toDate }}&Dimension={{ $dimension }}&Dimension2={{ $dimension2 }}&account={{ $da['code'] }}" class="text-indigo-600 hover:text-indigo-900">{{ $da['code'] }} {{ $da['name'] }}</a></td>
                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($da['per'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($da['acc'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($da['pct'], 1) }}%</td>
                    </tr>
                    @endforeach

                    {{-- Sub-type drilldown links --}}
                    @foreach($drilldownSubTypes as $dst)
                    @if($dst['per'] != 0 || $dst['acc'] != 0)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm"><a href="{{ route('banking.reports.profit-loss') }}?TransFromDate={{ $fromDate }}&TransToDate={{ $toDate }}&Compare={{ $compare }}&Dimension={{ $dimension }}&Dimension2={{ $dimension2 }}&AccGrp={{ $dst['type']->id }}" class="text-indigo-600 hover:text-indigo-900">{{ $dst['type']->id }} {{ $dst['type']->name }}</a></td>
                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($dst['displayPer'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($dst['displayAcc'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($dst['pct'], 1) }}%</td>
                    </tr>
                    @endif
                    @endforeach

                    {{-- Total for this drilldown group --}}
                    <tr class="bg-yellow-50 font-bold">
                        <td class="px-4 py-2 text-sm font-bold text-gray-700">Total {{ $accType ? $accType->name : '' }}</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($drilldownPerTotal, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($drilldownAccTotal, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($achieve($drilldownPerTotal, $drilldownAccTotal), 1) }}%</td>
                    </tr>

                    {{-- Back link --}}
                    <tr>
                        <td class="px-4 py-2 text-sm" colspan="4"><a href="{{ route('banking.reports.profit-loss') }}?TransFromDate={{ $fromDate }}&TransToDate={{ $toDate }}&Compare={{ $compare }}&Dimension={{ $dimension }}&Dimension2={{ $dimension2 }}" class="text-indigo-600 hover:text-indigo-900">&laquo; Back</a></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>
@endif
@endsection