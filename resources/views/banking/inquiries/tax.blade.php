@extends('layouts.app')
@section('title', 'Tax Inquiry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Tax Inquiry</h2>
</div>

<form method="POST" action="{{ route('banking.inquiries.tax') }}">
@csrf
<table class="mb-4">
    <tr>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">from:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransFromDate" value="{{ $fromDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1 pr-2 text-sm font-medium text-gray-700 whitespace-nowrap">to:</td>
        <td class="py-1 pr-4">
            <input type="date" name="TransToDate" value="{{ $toDate }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </td>
        <td class="py-1">
            <button type="submit" name="Show" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Show</button>
        </td>
    </tr>
</table>
</form>

<div id="trans_tbl">
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Outputs/Inputs</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($taxData as $td)
                    @php $tax = $td['tax']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $tax->name }} {{ number_format($tax->rate, 2) }}%</td>
                        <td class="px-3 py-2 text-sm text-gray-600">Charged on sales (Output Tax):</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($td['payable'], 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($td['net_output'], 2) }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $tax->name }} {{ number_format($tax->rate, 2) }}%</td>
                        <td class="px-3 py-2 text-sm text-gray-600">Paid on purchases (Input Tax):</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($td['displayCollectible'], 2) }}</td>
                        <td class="px-3 py-2 text-sm text-right text-gray-700">{{ number_format($td['displayNetInput'], 2) }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50 font-bold">
                        <td class="px-3 py-2 text-sm font-bold text-gray-700">{{ $tax->name }} {{ number_format($tax->rate, 2) }}%</td>
                        <td class="px-3 py-2 text-sm font-bold text-gray-700">Net payable or collectible:</td>
                        <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($td['net'], 2) }}</td>
                        <td class="px-3 py-2 text-sm"></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-gray-500">No tax types defined.</td>
                    </tr>
                @endforelse
                @if(count($taxData) > 0)
                <tr class="bg-yellow-50 font-bold">
                    <td class="px-3 py-2 text-sm font-bold text-gray-700"></td>
                    <td class="px-3 py-2 text-sm font-bold text-gray-700">Total payable or refund:</td>
                    <td class="px-3 py-2 text-sm text-right font-bold text-gray-700">{{ number_format($totalNet, 2) }}</td>
                    <td class="px-3 py-2 text-sm"></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection