@extends('layouts.app')
@section('title', 'Currencies - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Currencies</h2>
    <p class="mt-2 text-gray-600">Manage currencies.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.currencies') }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Abbreviation</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hundredths Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Auto update</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($currencies as $c)
                <tr class="hover:bg-gray-50 {{ $c->curr_abrev == $home ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $c->curr_abrev }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->curr_symbol }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->currency }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->hundreds_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->country }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        @if($c->curr_abrev == $home)
                            <span class="text-gray-400">-</span>
                        @else
                            <span class="{{ $c->auto_update ? 'text-green-600' : 'text-gray-500' }}">{{ $c->auto_update ? 'Yes' : 'No' }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('banking.currencies', ['toggle_inactive' => $c->curr_abrev, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $c->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $c->inactive ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $c->curr_abrev }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($c->curr_abrev != $home)
                            <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $c->curr_abrev }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No currencies defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mb-4">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id ? 'Edit Currency' : 'Add New Currency' }}
    </h3>

    <input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">

    <div class="space-y-4 max-w-lg">
        @if($selected_id)
            <input type="hidden" name="Abbreviation" value="{{ $abbreviation }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Abbreviation:</label>
                <p class="py-2 text-sm text-gray-800 font-medium">{{ $abbreviation }}</p>
            </div>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Abbreviation:</label>
                <input type="text" name="Abbreviation" value="" maxlength="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol:</label>
            <input type="text" name="Symbol" value="{{ old('Symbol', $symbol) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency Name:</label>
            <input type="text" name="CurrencyName" value="{{ old('CurrencyName', $currency_name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hundredths Name:</label>
            <input type="text" name="hundreds_name" value="{{ old('hundreds_name', $hundreds_name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Country:</label>
            <input type="text" name="country" value="{{ old('country', $country) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="flex items-center">
                <input type="checkbox" name="auto_update" value="1" {{ $auto_update ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Automatic exchange rate update</span>
            </label>
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

@if($home)
<p class="mt-4 text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded px-4 py-2">
    <i class="fas fa-info-circle mr-1"></i> The marked currency is the home currency which cannot be deleted.
</p>
@endif

</form>
@endsection