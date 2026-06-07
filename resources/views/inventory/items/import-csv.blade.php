@extends('layouts.app')
@section('title', 'Import of CSV formatted Items')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Import of CSV formatted Items</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@foreach($errors as $err)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $err }}</div>
@endforeach

<div class="mb-4 text-sm text-gray-700">
    @if($action == 'import')
        <span class="font-bold">Import</span>
    @else
        <a href="{{ route('inventory.items.import-csv', ['action' => 'import']) }}" class="text-indigo-600 hover:text-indigo-900">Import</a>
    @endif
    &nbsp;|&nbsp;
    @if($action == 'export')
        <span class="font-bold">Export</span>
    @else
        <a href="{{ route('inventory.items.import-csv', ['action' => 'export']) }}" class="text-indigo-600 hover:text-indigo-900">Export</a>
    @endif
</div>

@if($action == 'import')
<form method="POST" action="{{ route('inventory.items.import-csv') }}" enctype="multipart/form-data">
@csrf
<input type="hidden" name="action" value="import">

<div class="bg-white shadow rounded-lg p-6 mb-6" style="width:60%">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Default GL Accounts</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sales Account:</label>
            <select name="sales_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->account_code }}" {{ ($company_prefs['default_inv_sales_act'] ?? '') == $acc->account_code ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Account:</label>
            <select name="inventory_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->account_code }}" {{ ($company_prefs['default_inventory_act'] ?? '') == $acc->account_code ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">C.O.G.S. Account:</label>
            <select name="cogs_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->account_code }}" {{ ($company_prefs['default_cogs_act'] ?? '') == $acc->account_code ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Adjustments Account:</label>
            <select name="adjustment_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->account_code }}" {{ ($company_prefs['default_adj_act'] ?? '') == $acc->account_code ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Item Assembly Costs Account:</label>
            <select name="wip_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->account_code }}" {{ ($company_prefs['default_wip_act'] ?? '') == $acc->account_code ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6" style="width:60%">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Separator, Location, Tax and Sales Type</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Field separator:</label>
            <input type="text" name="sep" value="{{ old('sep', ',') }}" maxlength="1" class="w-16 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Location:</label>
            <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->loc_code }}">{{ $loc->location_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Item Tax Type:</label>
            <select name="tax_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="0">-- Select --</option>
                @foreach($taxTypes as $tt)
                    <option value="{{ $tt->id }}">{{ $tt->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sales Type:</label>
            <select name="sales_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach($salesTypes as $st)
                    <option value="{{ $st->id }}">{{ $st->type_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CSV Import File:</label>
            <input type="file" name="imp" id="imp" class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:rounded-md file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>
    </div>
</div>

<div class="text-center">
    <button type="submit" name="import" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Import CSV File</button>
</div>
</form>
@endif

@if($action == 'export')
<form method="POST" action="{{ route('inventory.items.import-csv') }}">
@csrf
<input type="hidden" name="action" value="export">
<input type="hidden" name="currency" value="{{ $company_prefs['curr_default'] ?? 'USD' }}">

<div class="bg-white shadow rounded-lg p-6 mb-6" style="width:40%">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Export Selection</h3>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Export Type:</label>
            <select name="export_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="1">Item</option>
                <option value="2">Price List</option>
                <option value="3">Purchase Price</option>
                <option value="4">Units of Measure</option>
                <option value="5">Kit</option>
                <option value="6">Bill of Materials</option>
                <option value="7">Foreign Item Codes</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sales Type (for Price Lists):</label>
            <select name="sales_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach($salesTypes as $st)
                    <option value="{{ $st->id }}">{{ $st->type_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="text-center">
    <button type="submit" name="export" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Export CSV File</button>
</div>
</form>
@endif
@endsection