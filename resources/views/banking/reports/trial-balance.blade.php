@extends('layouts.app')
@section('title', 'Trial Balance - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Trial Balance</h2>
</div>

<form method="POST" action="{{ route('banking.reports.trial-balance') }}">
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
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="NoZero" value="1" {{ $noZero ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">No zero values</span>
            </label>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="Balance" value="1" {{ $balanceOnly ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">Only balances</span>
            </label>
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="GroupTotalOnly" value="1" {{ $groupTotalOnly ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-1">Group totals only</span>
            </label>
        </td>
        <td class="py-1">
            <button type="submit" name="Show" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Show</button>
        </td>
    </tr>
</table>
</form>

@if($show && count($tree) > 0)
<div id="balance_tbl">
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Account</th>
                    <th rowspan="2" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                    <th colspan="2" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase border-l border-gray-200">Brought Forward</th>
                    <th colspan="2" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase border-l border-gray-200">This Period</th>
                    <th colspan="2" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase border-l border-gray-200">Balance</th>
                </tr>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase border-l border-gray-200 border-t border-gray-200">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase border-t border-gray-200">Credit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase border-l border-gray-200 border-t border-gray-200">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase border-t border-gray-200">Credit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase border-l border-gray-200 border-t border-gray-200">Debit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase border-t border-gray-200">Credit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($tree as $classNode)
                    <tr class="bg-indigo-50 font-bold">
                        <td class="px-3 py-2 text-sm font-bold text-gray-800" colspan="8">Class - {{ $classNode['class']->cid }} - {{ $classNode['class']->class_name }}</td>
                    </tr>
                    @foreach($classNode['types'] as $typeNode)
                        @php
                            $type = $typeNode['type'];
                            $hasContent = (!$groupTotalOnly && count($typeNode['accounts']) > 0) || count($typeNode['sub_types']) > 0;
                        @endphp
                        @if($hasContent)
                            @if(!$groupTotalOnly)
                            <tr class="bg-yellow-50 font-bold">
                                <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="8">Group - {{ $type->id }} - {{ $type->name }}</td>
                            </tr>
                            @endif
                            @if(!$groupTotalOnly)
                                @foreach($typeNode['accounts'] as $acc)
                                    @if(!$noZero || $acc['pbal'] != 0 || $acc['cbal'] != 0 || $acc['tbal'] != 0)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-sm"><a href="{{ route('banking.inquiries.gl') }}?TransFromDate={{ $fromDate }}&TransToDate={{ $toDate }}&account={{ $acc['code'] }}&Dimension={{ $dimension }}&Dimension2={{ $dimension2 }}" class="text-indigo-600 hover:text-indigo-900">{{ $acc['code'] }}</a></td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $acc['name'] }}</td>
                                        @if($balanceOnly)
                                            <td class="px-3 py-2 text-sm text-right text-gray-700" colspan="2">@if($acc['pbal'] >= 0){{ number_format($acc['pbal'], 2) }}@else<span class="text-red-600">{{ number_format(abs($acc['pbal']), 2) }} Cr</span>@endif</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700" colspan="2">@if($acc['cbal'] >= 0){{ number_format($acc['cbal'], 2) }}@else<span class="text-red-600">{{ number_format(abs($acc['cbal']), 2) }} Cr</span>@endif</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700" colspan="2">@if($acc['tbal'] >= 0){{ number_format($acc['tbal'], 2) }}@else<span class="text-red-600">{{ number_format(abs($acc['tbal']), 2) }} Cr</span>@endif</td>
                                        @else
                                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $acc['pdeb'] > 0 ? number_format($acc['pdeb'], 2) : '' }}</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $acc['pcre'] > 0 ? number_format($acc['pcre'], 2) : '' }}</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $acc['cdeb'] > 0 ? number_format($acc['cdeb'], 2) : '' }}</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $acc['ccre'] > 0 ? number_format($acc['ccre'], 2) : '' }}</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $acc['tdeb'] > 0 ? number_format($acc['tdeb'], 2) : '' }}</td>
                                            <td class="px-3 py-2 text-sm text-right text-gray-700">{{ $acc['tcre'] > 0 ? number_format($acc['tcre'], 2) : '' }}</td>
                                        @endif
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                            @foreach($typeNode['sub_types'] as $stNode)
                                @include('banking.reports.trial-balance-row', [
                                    'stNode' => $stNode,
                                    'fromDate' => $fromDate,
                                    'toDate' => $toDate,
                                    'dimension' => $dimension,
                                    'dimension2' => $dimension2,
                                    'noZero' => $noZero,
                                    'balanceOnly' => $balanceOnly,
                                    'groupTotalOnly' => $groupTotalOnly,
                                    'level' => 0
                                ])
                            @endforeach
                            <tr class="bg-indigo-50 font-bold">
                                <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="2">Total - {{ $type->name }}</td>
                                @if($balanceOnly)
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($typeNode['total_pbal'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($typeNode['total_cbal'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($typeNode['total_tbal'], 2) }}</td>
                                @else
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($typeNode['total_pdeb'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($typeNode['total_pcre'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($typeNode['total_cdeb'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($typeNode['total_ccre'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($typeNode['total_tdeb'], 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($typeNode['total_tcre'], 2) }}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                @endforeach

                <tr class="bg-indigo-50 font-bold">
                    <td class="px-3 py-2 text-sm font-bold text-gray-800" colspan="2">Total - {{ $toDate }}</td>
                    @if($balanceOnly)
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($gt_pbal, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($gt_cbal, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($gt_tbal, 2) }}</td>
                    @else
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_pdeb, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_pcre, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_cdeb, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_ccre, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_tdeb, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_tcre, 2) }}</td>
                    @endif
                </tr>
                <tr class="bg-indigo-50 font-bold">
                    <td class="px-3 py-2 text-sm font-bold text-gray-800" colspan="2">Ending Balance - {{ $toDate }}</td>
                    @if($balanceOnly)
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="6">{{ number_format($gt_tbal, 2) }}</td>
                    @else
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_pbal, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_cbal, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($gt_tbal, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700" colspan="3"></td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>

@if($gt_pbal != 0 && $dimension == 0 && $dimension2 == 0)
    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-800">
        The Opening Balance is not in balance, probably due to a non closed Previous Fiscalyear.
    </div>
@endif
</div>
@endif
@endsection