@extends('layouts.app')

@section('title', 'Inventory Item Where Used Inquiry - Manufacturing')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Inventory Item Where Used Inquiry</h2>
    </div>

    <form method="POST" action="{{ route('manufacturing.item-where-used') }}">
        @csrf
        <div class="text-center mb-4">
            <span class="text-sm text-gray-700">Select an item to display its parent item(s).</span>
            <select name="stock_id" onchange="this.form.submit()"
                    class="ml-2 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">Select Item</option>
                @foreach($items as $itemOpt)
                    <option value="{{ $itemOpt->code }}" {{ $stock_id == $itemOpt->code ? 'selected' : '' }}>{{ $itemOpt->code }} - {{ $itemOpt->name }}</option>
                @endforeach
            </select>
            <hr class="mt-2">
        </div>
    </form>

    @if($stock_id)
        @if($parents->count() > 0)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Parent Item</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Work Centre</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Location</th>
                            <th class="text-right px-4 py-2 font-medium text-gray-600">Quantity Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parents as $parent)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <a href="{{ route('manufacturing.bom.index', ['stock_id' => $parent->parent]) }}"
                                   class="text-blue-600 hover:text-blue-800">{{ $parent->parent }} - {{ $parent->description }}</a>
                            </td>
                            <td class="px-4 py-2">{{ $parent->WorkCentreName }}</td>
                            <td class="px-4 py-2">{{ $parent->location_name }}</td>
                            <td class="text-right px-4 py-2">{{ number_format($parent->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($parents->hasPages())
                    <div class="px-4 py-3 border-t">
                        {{ $parents->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500">
                No parent items found for the selected component.
            </div>
        @endif
    @endif
@endsection