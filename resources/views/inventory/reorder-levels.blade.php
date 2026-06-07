@extends('layouts.app')
@section('title', 'Reorder Levels - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Reorder Levels</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.reorder-levels') }}">
@csrf

<div class="text-center mb-4">
    <label class="text-sm font-medium text-gray-700 mr-2">Item:</label>
    <select name="stock_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">-- Select Item --</option>
        @foreach($items as $it)
            <option value="{{ $it->code }}" {{ $stock_id == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
        @endforeach
    </select>
    <label class="ml-4 text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-1">Show also inactive</span>
    </label>
    <hr class="mt-4">
</div>

@if($item)
<div class="bg-white shadow rounded-lg overflow-hidden mb-4 p-4">
    <table class="min-w-full text-sm">
        <tr>
            <td class="font-medium text-gray-700 pr-4">Item:</td>
            <td class="text-gray-900">{{ $item->code }} - {{ $item->name }}</td>
        </tr>
        @if($item->description)
        <tr>
            <td class="font-medium text-gray-700 pr-4">Description:</td>
            <td class="text-gray-600">{{ $item->description }}</td>
        </tr>
        @endif
    </table>
</div>
@endif

<div class="bg-white shadow rounded-lg overflow-hidden mb-6" style="width:30%">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity On Hand</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Re-Order Level</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($loc_data as $i => $ld)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $ld->loc->location_name }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">0.00</td>
                    <td class="px-4 py-3 text-right">
                        <input type="text" name="{{ $ld->loc->loc_code }}" value="{{ number_format($ld->reorder_level, 4) }}" class="w-28 text-right border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </td>
                </tr>
                @if(($i + 1) % 12 == 0 && !$loop->last)
                    <tr class="bg-gray-50"><td colspan="3" class="px-4 py-1"></td></tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity On Hand</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Re-Order Level</th>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<div class="text-center">
    <button type="submit" name="UpdateData" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
</div>

</form>
@endsection