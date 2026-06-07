@extends('layouts.app')
@section('title', 'Inventory Item Status')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Inventory Item Status</h2>
</div>

<form method="POST" action="{{ route('inventory.inquiries.status') }}">
@csrf

<div class="text-center mb-4">
    <label class="text-sm font-medium text-gray-700 mr-2">Item:</label>
    <select name="stock_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">-- Select --</option>
        @foreach($items as $it)
            <option value="{{ $it->code }}" {{ $stock_id == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
        @endforeach
    </select>
    <hr class="mt-4">
</div>

@if($stock_id)
@if($isService)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">This is a service and cannot have a stock holding, only the total quantity on outstanding sales orders is shown.</div>
@endif

<div id="status_tbl">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                @if(!$isService)
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity On Hand</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Re-Order Level</th>
                @endif
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Demand</th>
                @if(!$isService)
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Available</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">On Order</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @php $j = 1; $k = 0; $dec = 4; @endphp
            @forelse($locDetails as $loc)
                <tr class="{{ $k % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $loc->location_name }}</td>
                    @if(!$isService)
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($loc->qoh, $dec) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($loc->reorder_level, $dec) }}</td>
                    @endif
                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($loc->demand, $dec) }}</td>
                    @if(!$isService)
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($loc->available, $dec) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($loc->on_order, $dec) }}</td>
                    @endif
                </tr>
                @if($j % 12 == 0)
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        @if(!$isService)
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity On Hand</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Re-Order Level</th>
                        @endif
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Demand</th>
                        @if(!$isService)
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Available</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">On Order</th>
                        @endif
                    </tr>
                @endif
                @php $j++; $k++; @endphp
            @empty
                <tr>
                    <td colspan="{{ $isService ? 2 : 6 }}" class="px-4 py-6 text-center text-gray-500">No location details found for this item.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
</form>
@endsection