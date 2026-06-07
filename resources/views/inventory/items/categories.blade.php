@extends('layouts.app')
@section('title', 'Item Categories - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Item Categories</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.items.categories') }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6" style="width:80%">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                @if($fixed_asset)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Units</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sales Act</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asset Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Deprecation Cost Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Depreciation/Disposal Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                @else
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Units</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sales Act</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inventory Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">COGS Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Adjustment Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assembly Account</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $cat->description }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $cat->tax_name }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $cat->dflt_units }}</td>
                    @if(!$fixed_asset)<td class="px-4 py-3 text-sm text-center text-gray-700">{{ $stockTypes[$cat->dflt_mb_flag] ?? $cat->dflt_mb_flag }}</td>@endif
                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $cat->dflt_sales_act }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $cat->dflt_inventory_act }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $cat->dflt_cogs_act }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $cat->dflt_adjustment_act }}</td>
                    @if(!$fixed_asset)<td class="px-4 py-3 text-sm text-center text-gray-700">{{ $cat->dflt_wip_act }}</td>@endif
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('inventory.items.categories', ['toggle_inactive' => $cat->id, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $cat->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $cat->inactive ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $cat->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $cat->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $fixed_asset ? '10' : '12' }}" class="px-4 py-8 text-center text-gray-500">No item categories defined.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray-50">
                <td colspan="{{ $fixed_asset ? '9' : '11' }}" class="px-4 py-3">
                    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Show also inactive</span>
                    </label>
                </td>
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3"></td>
            </tr>
        </tfoot>
    </table>
</div>

<div id="details">
<div class="bg-white shadow rounded-lg p-6 max-w-lg">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Default values for new items</h3>

    <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @if($selected_id)<input type="hidden" name="category_id" value="{{ $selected_id }}">@endif

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name:</label>
            <input type="text" name="description" value="{{ $description }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <h4 class="text-base font-semibold text-gray-800 border-b border-gray-200 pb-1">Default values for new items</h4>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Item Tax Type:</label>
            <select name="tax_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- None --</option>
                @foreach($taxTypes as $t)
                    <option value="{{ $t->id }}" {{ $tax_type_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        @if($fixed_asset)
            <input type="hidden" name="mb_flag" value="F">
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Type:</label>
                <select name="mb_flag" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                    @foreach($stockTypes as $val => $label)
                        @if($val != 'F')
                        <option value="{{ $val }}" {{ $mb_flag == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Units of Measure:</label>
            <select name="units" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ $units_val == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        @if(!$fixed_asset)
        <div>
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="no_sale" value="1" {{ $no_sale ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm text-gray-700">Exclude from sales:</span>
            </label>
        </div>
        @endif

        <div>
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="no_purchase" value="1" {{ $no_purchase ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                <span class="ml-2 text-sm text-gray-700">Exclude from purchases:</span>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sales Account:</label>
            <select name="sales_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- None --</option>
                @foreach($glAccounts as $a)
                    <option value="{{ $a->code }}" {{ $sales_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                @endforeach
            </select>
        </div>

        @if($fixed_asset)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Asset account:</label>
                <select name="inventory_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $inventory_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depreciation cost account:</label>
                <select name="cogs_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $cogs_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depreciation/Disposal account:</label>
                <select name="adjustment_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $adjustment_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="inventory_account" value="{{ $inventory_account }}">
            <input type="hidden" name="adjustment_account" value="{{ $adjustment_account }}">
        @elseif($is_service)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">C.O.G.S. Account:</label>
                <select name="cogs_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $cogs_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="inventory_account" value="{{ $inventory_account }}">
            <input type="hidden" name="adjustment_account" value="{{ $adjustment_account }}">
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Account:</label>
                <select name="inventory_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $inventory_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">C.O.G.S. Account:</label>
                <select name="cogs_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $cogs_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Adjustments Account:</label>
                <select name="adjustment_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $adjustment_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if($is_manufactured)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Assembly Costs Account:</label>
                <select name="wip_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($glAccounts as $a)
                        <option value="{{ $a->code }}" {{ $wip_account == $a->code ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <input type="hidden" name="wip_account" value="{{ $wip_account }}">
        @endif

        @if($use_dimension >= 1)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 1:</label>
                <select name="dim1" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0"> </option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $dim1 == $d->id ? 'selected' : '' }}>{{ $d->code ? $d->code . ' - ' : '' }}{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($use_dimension > 1)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 2:</label>
                <select name="dim2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0"> </option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $dim2 == $d->id ? 'selected' : '' }}>{{ $d->code ? $d->code . ' - ' : '' }}{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        @endif
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
</div>

</form>
@endsection