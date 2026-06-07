@extends('layouts.app')

@section('title', $outstanding_only ? 'Search Outstanding Work Orders - Manufacturing' : 'Search Work Orders - Manufacturing')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ $outstanding_only ? 'Search Outstanding Work Orders' : 'Search Work Orders' }}</h2>
    </div>

    <form method="POST" action="{{ route('manufacturing.outstanding-work-orders', ['outstanding_only' => $outstanding_only]) }}">
        @csrf
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600">#:</label>
                    <input type="text" name="OrderId" value="{{ $OrderId }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Reference:</label>
                    <input type="text" name="OrderNumber" value="{{ $OrderNumber }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                           onchange="this.form.submit()">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">at Location:</label>
                    <select name="StockLocation" {{ $disableFilters ? 'disabled' : '' }}
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->loc_code }}" {{ $StockLocation == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="OverdueOnly" id="OverdueOnly" value="1" {{ $OverdueOnly ? 'checked' : '' }} {{ $disableFilters ? 'disabled' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="OverdueOnly" class="ml-2 text-sm text-gray-700">Only Overdue:</label>
                </div>
                @if(!$outstanding_only)
                <div class="flex items-center">
                    <input type="checkbox" name="OpenOnly" id="OpenOnly" value="1" {{ $OpenOnly ? 'checked' : '' }} {{ $disableFilters ? 'disabled' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="OpenOnly" class="ml-2 text-sm text-gray-700">Only Open:</label>
                </div>
                @endif
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-600">for item:</label>
                    <select name="SelectedStockItem" {{ $disableFilters ? 'disabled' : '' }}
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All</option>
                        @foreach($manufactured_items as $item)
                            <option value="{{ $item->code }}" {{ $SelectedStockItem == $item->code ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" name="SearchOrders" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Search</button>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="text-left px-3 py-2 font-medium text-gray-600">#</th>
                    <th class="text-left px-3 py-2 font-medium text-gray-600">Reference</th>
                    <th class="text-left px-3 py-2 font-medium text-gray-600">Type</th>
                    <th class="text-left px-3 py-2 font-medium text-gray-600">Location</th>
                    <th class="text-left px-3 py-2 font-medium text-gray-600">Item</th>
                    <th class="text-right px-3 py-2 font-medium text-gray-600">Required</th>
                    <th class="text-right px-3 py-2 font-medium text-gray-600">Manufactured</th>
                    <th class="text-left px-3 py-2 font-medium text-gray-600">Date</th>
                    <th class="text-left px-3 py-2 font-medium text-gray-600">Required By</th>
                    <th class="text-center px-3 py-2 font-medium text-gray-600">GL</th>
                    <th class="text-center px-3 py-2 font-medium text-gray-600">Edit</th>
                    <th class="text-center px-3 py-2 font-medium text-gray-600">Release/Issue</th>
                    <th class="text-center px-3 py-2 font-medium text-gray-600">Costs</th>
                    <th class="text-center px-3 py-2 font-medium text-gray-600">Produce</th>
                    <th class="text-center px-3 py-2 font-medium text-gray-600">Print</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                @php
                    $isOverdue = !$wo->closed && $wo->required_by && \Carbon\Carbon::parse($wo->required_by)->isPast();
                @endphp
                <tr class="border-b hover:bg-gray-50 {{ $isOverdue ? 'bg-red-50' : '' }}">
                    <td class="px-3 py-2">
                        <a href="{{ route('manufacturing.work-order-entry', ['trans_no' => $wo->id]) }}"
                           class="text-blue-600 hover:text-blue-800">{{ $wo->id }}</a>
                    </td>
                    <td class="px-3 py-2">{{ $wo->wo_ref }}</td>
                    <td class="px-3 py-2">{{ $wo_types[$wo->type] ?? 'Unknown' }}</td>
                    <td class="px-3 py-2">{{ $wo->location_name }}</td>
                    <td class="px-3 py-2">
                        <span title="{{ $wo->description }}">{{ $wo->stock_id }} - {{ $wo->description }}</span>
                    </td>
                    <td class="text-right px-3 py-2">{{ number_format($wo->units_reqd, $wo->decimals) }}</td>
                    <td class="text-right px-3 py-2">{{ number_format($wo->units_issued, $wo->decimals) }}</td>
                    <td class="px-3 py-2">{{ $wo->date_ ? \Carbon\Carbon::parse($wo->date_)->format('d/m/Y') : '' }}</td>
                    <td class="px-3 py-2">{{ $wo->required_by ? \Carbon\Carbon::parse($wo->required_by)->format('d/m/Y') : '' }}</td>
                    <td class="text-center px-3 py-2">
                        <a href="{{ route('manufacturing.work-order-entry', ['trans_no' => $wo->id]) }}"
                           class="text-blue-600 hover:text-blue-800">GL</a>
                    </td>
                    <td class="text-center px-3 py-2">
                        @if($wo->closed)
                            <span class="text-gray-500 italic text-xs">Closed</span>
                        @else
                            <a href="{{ route('manufacturing.work-order-entry', ['selected_id' => $wo->id]) }}"
                               class="text-blue-600 hover:text-blue-800">Edit</a>
                        @endif
                    </td>
                    <td class="text-center px-3 py-2">
                        @if(!$wo->closed)
                            @if(!$wo->released)
                                <a href="{{ route('manufacturing.work-order-release', $wo->id) }}"
                                   class="text-green-600 hover:text-green-800">Release</a>
                            @else
                                <a href="{{ route('manufacturing.work-order-issue', $wo->id) }}"
                                   class="text-yellow-600 hover:text-yellow-800">Issue</a>
                            @endif
                        @endif
                    </td>
                    <td class="text-center px-3 py-2">
                        @if($wo->released && !$wo->closed)
                            <a href="{{ route('manufacturing.work-order-costs', $wo->id) }}"
                               class="text-blue-600 hover:text-blue-800">Costs</a>
                        @endif
                    </td>
                    <td class="text-center px-3 py-2">
                        @if($wo->released && !$wo->closed)
                            <a href="{{ route('manufacturing.work-order-produce', $wo->id) }}"
                               class="text-purple-600 hover:text-purple-800">Produce</a>
                        @endif
                    </td>
                    <td class="text-center px-3 py-2">
                        <a href="{{ route('manufacturing.work-order-print', $wo->id) }}"
                           class="text-blue-600 hover:text-blue-800 text-xs" title="Print">Print</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="text-center py-8 text-gray-500">No work orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($workOrders->hasPages())
            <div class="px-3 py-3 border-t">
                {{ $workOrders->withQueryString()->links() }}
            </div>
        @endif
    </div>

    @if($isOverdue ?? false)
    @endif
    <div class="mt-2 text-xs text-gray-500 italic">Marked orders are overdue.</div>
@endsection