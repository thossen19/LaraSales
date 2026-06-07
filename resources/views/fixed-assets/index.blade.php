@extends('layouts.app')

@section('title', 'Fixed Assets')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Fixed Assets</h2>
    </div>

    @if($msg)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('fixed-assets.index') }}">
        @csrf
        @if($fixed_assets->count() > 0)
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600">Select an item:</label>
                    <select name="stock_id" onchange="this.form.submit()"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">New item</option>
                        @foreach($fixed_assets as $fa)
                            <option value="{{ $fa->code }}" {{ $stock_id == $fa->code ? 'selected' : '' }}>{{ $fa->code }} - {{ $fa->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center mt-4">
                    <input type="checkbox" name="show_inactive" value="1" id="show_inactive"
                           {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="show_inactive" class="ml-2 text-sm text-gray-700">Show inactive:</label>
                </div>
            </div>
        </div>
        @else
            <input type="hidden" name="stock_id" value="">
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <input type="hidden" name="mb_flag" value="F">
            <input type="hidden" name="fixed_asset" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($new_item)
                <input type="hidden" name="_new_item" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Item Code:</label>
                    <div class="mt-1 flex gap-2">
                        <input type="text" name="NewStockID" maxlength="20"
                               value="{{ old('NewStockID') }}"
                               class="block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" onclick="generateEan8()"
                                class="flex-shrink-0 px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-200 focus:outline-none">
                            Generate EAN-8 Barcode
                        </button>
                    </div>
                </div>
                @else
                    <input type="hidden" name="NewStockID" value="{{ $selected_asset->code ?? '' }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Item Code:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $selected_asset->code ?? '' }}</p>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" name="description" maxlength="200"
                           value="{{ old('description', $selected_asset->name ?? '') }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="long_description" rows="3" maxlength="1000"
                          class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('long_description', $selected_asset->long_description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category:</label>
                    <select name="category_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        @foreach($stock_categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $selected_asset->category ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Item Tax Type:</label>
                    <select name="tax_type_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($tax_types as $tt)
                            <option value="{{ $tt->id }}" {{ old('tax_type_id', $selected_asset->tax_type_id ?? '') == $tt->id ? 'selected' : '' }}>{{ $tt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Units of Measure:</label>
                    <select name="units"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($units as $u)
                            <option value="{{ $u->name }}" {{ old('units', $selected_asset->unit_of_measure ?? 'each') == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $selected_asset->is_active ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <h4 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Depreciation</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fixed Asset Class:</label>
                    <select name="fa_class_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($fa_classes as $fc)
                            <option value="{{ $fc->fa_class_id }}" {{ old('fa_class_id', $selected_asset->fa_class_id ?? 0) == $fc->fa_class_id ? 'selected' : '' }}>{{ $fc->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation Method:</label>
                    <select name="depreciation_method" onchange="this.form.submit()"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($depreciation_methods as $key => $label)
                            <option value="{{ $key }}" {{ old('depreciation_method', $selected_asset->depreciation_method ?? 'S') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                    $dep_method = old('depreciation_method', $selected_asset->depreciation_method ?? 'S');
                @endphp
                @if($dep_method == 'O')
                    <input type="hidden" name="depreciation_rate" value="100">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Depreciation Rate:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">100 %</p>
                    </div>
                @elseif($dep_method == 'N')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Depreciation Years:</label>
                        <input type="number" name="depreciation_rate" step="any" min="0"
                               value="{{ old('depreciation_rate', $selected_asset->depreciation_rate ?? 0) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                @elseif($dep_method == 'D')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Base Rate:</label>
                        <input type="number" name="depreciation_rate" step="any" min="0" max="100"
                               value="{{ old('depreciation_rate', $selected_asset->depreciation_rate ?? 0) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rate multiplier:</label>
                        <input type="number" name="depreciation_factor" step="any" min="0"
                               value="{{ old('depreciation_factor', $selected_asset->depreciation_factor ?? 1) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Depreciation Rate:</label>
                        <input type="number" name="depreciation_rate" step="any" min="0" max="100"
                               value="{{ old('depreciation_rate', $selected_asset->depreciation_rate ?? 0) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation Start:</label>
                    <input type="date" name="depreciation_start"
                           value="{{ old('depreciation_start', optional($selected_asset)->depreciation_start ? \Carbon\Carbon::parse(optional($selected_asset)->depreciation_start)->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <h4 class="text-lg font-semibold text-gray-800 mt-6 mb-3">GL Accounts</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sales Account:</label>
                    <select name="sales_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('sales_account', $selected_asset->sales_account ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asset account:</label>
                    <select name="inventory_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('inventory_account', $selected_asset->inventory_account ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation cost account:</label>
                    <select name="cogs_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('cogs_account', $selected_asset->cogs_account ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation/Disposal account:</label>
                    <select name="adjustment_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('adjustment_account', $selected_asset->adjustment_account ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(!$new_item && $selected_asset)
            <h4 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Values</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Initial Value:</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ number_format($selected_asset->purchase_cost ?? 0, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciations:</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ number_format(($selected_asset->purchase_cost ?? 0) - ($selected_asset->material_cost ?? 0), 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Value:</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ number_format($selected_asset->material_cost ?? 0, 2) }}</p>
                </div>
            </div>
            @endif

            <div class="mt-6 flex gap-2">
                @if($new_item)
                    <button type="submit" name="addupdate" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Insert New Item</button>
                @else
                    <button type="submit" name="addupdate" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Item</button>
                    <button type="submit" name="delete" value="1"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                            onclick="return confirm('Are you sure you want to delete this fixed asset?')">Delete This Item</button>
                    <button type="submit" name="cancel" value="1"
                            class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                @endif
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function generateEan8() {
    let digits = '';
    for (let i = 0; i < 7; i++) {
        digits += Math.floor(Math.random() * 10);
    }
    let sum = 0;
    for (let i = 0; i < 7; i++) {
        sum += parseInt(digits[i]) * (i % 2 === 0 ? 3 : 1);
    }
    let check = (10 - (sum % 10)) % 10;
    document.querySelector('input[name="NewStockID"]').value = digits + check;
}
</script>
@endpush