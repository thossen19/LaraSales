@extends('layouts.app')
@section('title', 'Items - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Items</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.items.index') }}" enctype="multipart/form-data">
@csrf

@if(count($items) > 0)
<div class="flex items-center gap-4 mb-4">
    <select name="stock_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">-- New item --</option>
        @foreach($items as $it)
            <option value="{{ $it->code }}" {{ $stock_id == $it->code ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
        @endforeach
    </select>
    <label class="text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-1">Show inactive:</span>
    </label>
</div>
@else
    <input type="hidden" name="stock_id" value="">
@endif

<div id="details">
    @php
        $is_new = !$stock_id;
        $fixed_asset = $item && $item->mb_flag == 'F';
        $is_service = $item && $item->mb_flag == 'D';
        $is_manufactured = $item && $item->mb_flag == 'M';
    @endphp

    <div class="tabs mb-4 border-b border-gray-200">
        <span class="inline-block px-4 py-2 bg-indigo-100 text-indigo-700 font-medium rounded-t border-t border-l border-r border-gray-200 -mb-px">General settings</span>
        <span class="inline-block px-4 py-2 text-gray-500">Sales Pricing</span>
        <span class="inline-block px-4 py-2 text-gray-500">Purchasing Pricing</span>
        <span class="inline-block px-4 py-2 text-gray-500">Standard Costs</span>
        <span class="inline-block px-4 py-2 text-gray-500">Reorder Levels</span>
        <span class="inline-block px-4 py-2 text-gray-500">Transactions</span>
        <span class="inline-block px-4 py-2 text-gray-500">Status</span>
        <span class="inline-block px-4 py-2 text-gray-500">Attachments</span>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <table class="w-full">
            <tr>
                <td class="align-top pr-8 w-1/2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">General Settings</h3>
                    <div class="space-y-4">
                        @if($is_new)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Code:</label>
                                <input type="text" name="NewStockID" value="{{ request('NewStockID', '') }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Code:</label>
                                <div class="text-sm text-gray-900 font-medium py-2">{{ $item->code ?? '' }}</div>
                                <input type="hidden" name="NewStockID" value="{{ $item->code ?? '' }}">
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name:</label>
                            <input type="text" name="description" value="{{ $is_new ? request('description', '') : ($item->name ?? '') }}" maxlength="200" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                            <textarea name="long_description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $is_new ? request('long_description', '') : ($item->long_description ?: $item->description ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                            @php $catId = $is_new ? (int) request('category_id', 0) : (int) ($item->category_id ?? 0); @endphp
                            <select name="category_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="0">-- Select --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $catId == $cat->id ? 'selected' : '' }}>{{ $cat->description }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Tax Type:</label>
                            <select name="tax_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @php $taxVal = $is_new ? request('tax_type_id', $autoFill['tax_type_id'] ?? 0) : ($item->tax_type_id ?? 0); @endphp
                                @foreach($taxTypes as $tt)
                                    <option value="{{ $tt->id }}" {{ $taxVal == $tt->id ? 'selected' : '' }}>{{ $tt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Type:</label>
                            <select name="mb_flag" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @php $mbVal = $is_new ? request('mb_flag', $autoFill['mb_flag'] ?? 'B') : ($item->mb_flag ?? 'B'); @endphp
                                @foreach($stockTypes as $k => $v)
                                    <option value="{{ $k }}" {{ $mbVal == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Units of Measure:</label>
                            <select name="units" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @php $unitVal = $is_new ? request('units', $autoFill['units'] ?? 'each') : ($item->unit_of_measure ?? 'each'); @endphp
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ $unitVal == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2 pt-2">
                            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="editable" value="1" {{ $is_new ? (request('editable') ? 'checked' : '') : (($item->editable ?? false) ? 'checked' : '') }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Editable description in transaction</span>
                            </label>
                            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                @php $noSaleVal = $is_new ? (request('no_sale') ? true : ($autoFill['no_sale'] ?? false)) : ($item->no_sale ?? false); @endphp
                                <input type="checkbox" name="no_sale" value="1" {{ $noSaleVal ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Exclude from sales</span>
                            </label>
                            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                @php $noPurVal = $is_new ? (request('no_purchase') ? true : ($autoFill['no_purchase'] ?? false)) : ($item->no_purchase ?? false); @endphp
                                <input type="checkbox" name="no_purchase" value="1" {{ $noPurVal ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Exclude from purchases</span>
                            </label>
                        </div>
                    </div>
                </td>
                <td class="align-top w-1/2">
                    @if($use_dimension >= 1)
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Dimensions</h3>
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 1:</label>
                                @php $dim1Val = $is_new ? request('dimension_id', $autoFill['dimension_id'] ?? 0) : ($item->dimension_id ?? 0); @endphp
                                <select name="dimension_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="0"> </option>
                                    @foreach($dimensions as $d)
                                        <option value="{{ $d->id }}" {{ $dim1Val == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($use_dimension > 1)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 2:</label>
                                @php $dim2Val = $is_new ? request('dimension2_id', $autoFill['dimension2_id'] ?? 0) : ($item->dimension2_id ?? 0); @endphp
                                <select name="dimension2_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="0"> </option>
                                    @foreach($dimensions as $d)
                                        <option value="{{ $d->id }}" {{ $dim2Val == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">GL Accounts</h3>
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sales Account:</label>
                            @php $saVal = $is_new ? request('sales_account', $autoFill['sales_account'] ?? '') : ($item->sales_account ?? ''); @endphp
                            <select name="sales_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select --</option>
                                @foreach($glAccounts as $acc)
                                    <option value="{{ $acc->code }}" {{ $saVal == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(!$is_service || $is_new)
                            @if(!$fixed_asset)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Account:</label>
                                @php $iaVal = $is_new ? request('inventory_account', $autoFill['inventory_account'] ?? '') : ($item->inventory_account ?? ''); @endphp
                                <select name="inventory_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Select --</option>
                                    @foreach($glAccounts as $acc)
                                        <option value="{{ $acc->code }}" {{ $iaVal == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        @else
                            <input type="hidden" name="inventory_account" value="{{ $item->inventory_account ?? '' }}">
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">C.O.G.S. Account:</label>
                            @php $cogsVal = $is_new ? request('cogs_account', $autoFill['cogs_account'] ?? '') : ($item->cogs_account ?? ''); @endphp
                            <select name="cogs_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select --</option>
                                @foreach($glAccounts as $acc)
                                    <option value="{{ $acc->code }}" {{ $cogsVal == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(!$is_service || $is_new)
                            @if(!$fixed_asset)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Adjustments Account:</label>
                                @php $adjVal = $is_new ? request('adjustment_account', $autoFill['adjustment_account'] ?? '') : ($item->adjustment_account ?? ''); @endphp
                                <select name="adjustment_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Select --</option>
                                    @foreach($glAccounts as $acc)
                                        <option value="{{ $acc->code }}" {{ $adjVal == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        @else
                            <input type="hidden" name="adjustment_account" value="{{ $item->adjustment_account ?? '' }}">
                        @endif

                        @php $showWip = $is_new ? (request('mb_flag', $autoFill['mb_flag'] ?? 'B') == 'M') : ($item->mb_flag ?? '') == 'M'; @endphp
                        @if($showWip)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WIP Account:</label>
                            @php $wipVal = $is_new ? request('wip_account', $autoFill['wip_account'] ?? '') : ($item->wip_account ?? ''); @endphp
                            <select name="wip_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select --</option>
                                @foreach($glAccounts as $acc)
                                    <option value="{{ $acc->code }}" {{ $wipVal == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" name="wip_account" value="{{ $item->wip_account ?? '' }}">
                        @endif
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Other</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Image File (.jpg):</label>
                            <input type="file" name="pic" class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:rounded-md file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        @if($item && \Storage::exists('public/items/' . $item->code . '.jpg'))
                            <div class="text-center">
                                <img src="{{ asset('storage/items/' . $item->code . '.jpg') }}" class="max-w-full h-auto mx-auto" style="max-height:150px">
                            </div>
                            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="del_image" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Delete Image:</span>
                            </label>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item status:</label>
                            <select name="inactive" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="0" {{ (!$is_new && $item && !$item->is_active) ? '' : 'selected' }}>Active</option>
                                <option value="1" {{ (!$is_new && $item && !$item->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center mt-6 space-x-2">
        @if($is_new)
            <button type="submit" name="addupdate" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Insert New Item</button>
        @else
            <button type="submit" name="addupdate" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update Item</button>
            <button type="submit" name="clone" value="1" class="px-6 py-2 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 transition">Clone This Item</button>
            <button type="submit" name="delete" value="1" onclick="return confirm('Are you sure?')" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition">Delete This Item</button>
            <button type="submit" name="cancel" value="1" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
        @endif
    </div>
</div>

</form>
@endsection