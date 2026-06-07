@if(!$groupTotalOnly)
<tr class="bg-yellow-50 font-bold">
    <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="8">Group - {{ $stNode['type']->id }} - {{ $stNode['type']->name }}</td>
</tr>
@foreach($stNode['accounts'] as $acc)
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

@foreach($stNode['sub_types'] as $childSt)
    @include('banking.reports.trial-balance-row', [
        'stNode' => $childSt,
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'dimension' => $dimension,
        'dimension2' => $dimension2,
        'noZero' => $noZero,
        'balanceOnly' => $balanceOnly,
        'groupTotalOnly' => $groupTotalOnly,
        'level' => $level + 1
    ])
@endforeach

@if($stNode['total_pdeb'] != 0 || $stNode['total_pcre'] != 0 || $stNode['total_cdeb'] != 0 || $stNode['total_ccre'] != 0 || $stNode['total_tdeb'] != 0 || $stNode['total_tcre'] != 0)
<tr class="bg-indigo-50 font-bold">
    <td class="px-3 py-2 text-sm font-bold text-gray-700" colspan="2">Total - {{ $stNode['type']->name }}</td>
    @if($balanceOnly)
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($stNode['total_pbal'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($stNode['total_cbal'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700" colspan="2">{{ number_format($stNode['total_tbal'], 2) }}</td>
    @else
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($stNode['total_pdeb'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($stNode['total_pcre'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($stNode['total_cdeb'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($stNode['total_ccre'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($stNode['total_tdeb'], 2) }}</td>
        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($stNode['total_tcre'], 2) }}</td>
    @endif
</tr>
@endif