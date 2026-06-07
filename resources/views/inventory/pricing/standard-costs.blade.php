@extends('layouts.app')
@section('title', 'Inventory Item Cost Update')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Inventory Item Cost Update</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.pricing.standard-costs') }}">
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
<div id="cost_table">
    <div class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit cost:</label>
                <input type="text" name="material_cost" value="{{ number_format($material_cost, 4) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            @if($is_manufactured)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Standard Labour Cost Per Unit:</label>
                <input type="text" name="labour_cost" value="{{ number_format($labour_cost, 4) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Standard Overhead Cost Per Unit:</label>
                <input type="text" name="overhead_cost" value="{{ number_format($overhead_cost, 4) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            @else
                <input type="hidden" name="labour_cost" value="0">
                <input type="hidden" name="overhead_cost" value="0">
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference line:</label>
                <select name="refline" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($reflines as $rl)
                        <option value="{{ $rl->id }}" {{ request('refline') == $rl->id ? 'selected' : '' }}>{{ $rl->description }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Memo:</label>
                <textarea name="memo_" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ request('memo_') }}</textarea>
            </div>
        </div>
    </div>

    <div class="text-center mt-6">
        <button type="submit" name="UpdateData" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
    </div>
</div>
@endif

</form>
@endsection