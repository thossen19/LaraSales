@extends('layouts.app')

@section('title', 'Costed Bill Of Material Inquiry - Manufacturing')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Costed Bill Of Material Inquiry</h2>
    </div>

    <form method="POST" action="{{ route('manufacturing.costed-bom-inquiry') }}">
        @csrf
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <div class="max-w-md">
                <label class="block text-sm font-medium text-gray-700">Select a manufacturable item:</label>
                <select name="stock_id" onchange="this.form.submit()"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Item</option>
                    @foreach($manufactured_items as $itemOpt)
                        <option value="{{ $itemOpt->code }}" {{ $stock_id == $itemOpt->code ? 'selected' : '' }}>{{ $itemOpt->code }} - {{ $itemOpt->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if($stock_id)
        @if($bom_items->count() > 0)
            <div class="text-sm text-gray-600 mb-2">All Costs Are In: {{ App\Models\Currency::first()->curr_abrev ?? 'USD' }}</div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Component</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Description</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Work Centre</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">From Location</th>
                            <th class="text-right px-4 py-2 font-medium text-gray-600">Quantity</th>
                            <th class="text-right px-4 py-2 font-medium text-gray-600">Unit Cost</th>
                            <th class="text-right px-4 py-2 font-medium text-gray-600">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $k = 0; @endphp
                        @foreach($bom_items as $bom)
                        <tr class="border-b hover:bg-gray-50 {{ $k % 2 ? 'bg-gray-50' : '' }}">
                            <td class="px-4 py-2">{{ $bom->component }}</td>
                            <td class="px-4 py-2">{{ $bom->description }}</td>
                            <td class="px-4 py-2">{{ $bom->WorkCentreDescription }}</td>
                            <td class="px-4 py-2">{{ $bom->location_name }}</td>
                            <td class="text-right px-4 py-2">{{ number_format($bom->quantity, 2) }}</td>
                            <td class="text-right px-4 py-2">{{ number_format($bom->ProductCost, 2) }}</td>
                            <td class="text-right px-4 py-2">{{ number_format($bom->ComponentCost, 2) }}</td>
                        </tr>
                        @php $k++; @endphp
                        @endforeach
                    </tbody>
                </table>

                <div class="px-4 py-3 border-t">
                    @if($item && $item->labour_cost > 0)
                    <div class="flex justify-between text-sm py-1">
                        <span class="font-medium">Standard Labour Cost</span>
                        <span>{{ number_format($item->labour_cost, 2) }}</span>
                    </div>
                    @endif
                    @if($item && $item->overhead_cost > 0)
                    <div class="flex justify-between text-sm py-1">
                        <span class="font-medium">Standard Overhead Cost</span>
                        <span>{{ number_format($item->overhead_cost, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm font-bold py-1 border-t mt-1 pt-1">
                        <span>Total Cost</span>
                        <span>{{ number_format($total_cost, 2) }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500">
                The bill of material for this item is empty.
            </div>
        @endif
    @endif
@endsection