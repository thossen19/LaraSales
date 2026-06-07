@extends('layouts.app')
@section('title', 'Exchange Rates - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Exchange Rates</h2>
    <p class="mt-2 text-gray-600">Manage currency exchange rates.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.exchange-rates') }}">
@csrf
<input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">

<div class="mb-6 text-center">
    <label class="text-gray-700 font-medium mr-2">Select a currency :</label>
    <select name="curr_abrev" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        @foreach($currencies as $c)
            <option value="{{ $c->curr_abrev }}" {{ $curr_abrev == $c->curr_abrev ? 'selected' : '' }}>{{ $c->currency }} ({{ $c->curr_abrev }})</option>
        @endforeach
    </select>
</div>

@if($is_home)
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded mb-4">
        <i class="fas fa-info-circle mr-1"></i> The selected currency is the company currency.
    </div>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4">
        <i class="fas fa-exclamation-triangle mr-1"></i> The company currency is the base currency so exchange rates cannot be set for it.
    </div>
@else
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6 max-w-xl mx-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date to Use From</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Exchange Rate</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rates as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $r->date_ }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-right font-mono">{{ number_format($r->rate_buy, 6) }}</td>
                        <td class="px-4 py-3 text-center">
                            <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $r->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $r->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No exchange rates defined for this currency.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
            {{ $selected_id ? 'Edit Exchange Rate' : 'Add New Exchange Rate' }}
        </h3>

        <div class="space-y-4">
            @if($selected_id)
                <input type="hidden" name="date_" value="{{ $edit_date }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date to Use From:</label>
                    <p class="py-2 text-sm text-gray-800 font-medium">{{ $edit_date }}</p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date to Use From:</label>
                    <input type="date" name="date_" value="{{ old('date_', $edit_date) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exchange Rate:</label>
                <div class="flex gap-2">
                    <input type="text" name="BuyRate" value="{{ old('BuyRate', $edit_buyrate) }}" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono" placeholder="0.000000">
                    <button type="submit" name="get_rate" value="1" class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200 text-sm">Get</button>
                </div>
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200">
            @if($selected_id)
                <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
                <button type="submit" name="Mode" value="RESET" class="px-6 py-2 ml-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
            @else
                <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
            @endif
        </div>
    </div>

    <p class="mt-4 text-sm text-gray-500 text-center">
        <i class="fas fa-info-circle mr-1"></i> Exchange rates are entered against the company currency.
    </p>
@endif

</form>
@endsection