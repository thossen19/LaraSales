@extends('layouts.app')

@section('title', 'Fixed Assets Categories')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Fixed Assets Categories</h2>
    </div>

    @if($msg)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('fixed-assets.categories') }}">
        @csrf
        <div class="bg-white shadow rounded-lg overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Tax type</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Units</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Sales Act</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Asset Account</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Deprecation Cost Account</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Depreciation/Disposal Account</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Inactive</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $k = 0; @endphp
                    @forelse($categories as $cat)
                    <tr class="border-b hover:bg-gray-50 {{ $k % 2 ? 'bg-gray-50' : '' }}">
                        <td class="px-4 py-2">{{ $cat->description }}</td>
                        <td class="px-4 py-2">{{ $cat->tax_name ?? '' }}</td>
                        <td class="text-center px-4 py-2">{{ $cat->dflt_units }}</td>
                        <td class="text-center px-4 py-2">{{ $cat->dflt_sales_act }}</td>
                        <td class="text-center px-4 py-2">{{ $cat->dflt_inventory_act }}</td>
                        <td class="text-center px-4 py-2">{{ $cat->dflt_cogs_act }}</td>
                        <td class="text-center px-4 py-2">{{ $cat->dflt_adjustment_act }}</td>
                        <td class="text-center px-4 py-2">
                            <input type="checkbox" {{ $cat->inactive ? 'checked' : '' }} disabled
                                   class="rounded border-gray-300">
                        </td>
                        <td class="text-center px-4 py-2">
                            <button type="submit" name="Edit{{ $cat->id }}" value="1"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                        </td>
                        <td class="text-center px-4 py-2">
                            <button type="submit" name="Delete{{ $cat->id }}" value="1"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                    onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                        </td>
                    </tr>
                    @php $k++; @endphp
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-8 text-gray-500">No fixed asset categories defined.</td>
                    </tr>
                    @endforelse
                    <tr class="bg-gray-50 border-t">
                        <td colspan="10" class="px-4 py-2">
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">Show Inactive</span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <input type="hidden" name="mb_flag" value="F">

            @if($selected_id != -1 && $selected_category)
                <input type="hidden" name="selected_id" value="{{ $selected_id }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category Name:</label>
                    <input type="text" name="description" maxlength="30"
                           value="{{ old('description', $selected_category->description ?? '') }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Item Tax Type:</label>
                    <select name="tax_type_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($tax_types as $tt)
                            <option value="{{ $tt->id }}" {{ old('tax_type_id', $selected_category->dflt_tax_type ?? '') == $tt->id ? 'selected' : '' }}>{{ $tt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-3">Default values for new items</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Units of Measure:</label>
                    <select name="units"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($units as $u)
                            <option value="{{ $u->name }}" {{ old('units', $selected_category->dflt_units ?? 'each') == $u->name ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="no_purchase" value="1"
                               {{ old('no_purchase', $selected_category->dflt_no_purchase ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Exclude from purchases:</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sales Account:</label>
                    <select name="sales_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('sales_account', $selected_category->dflt_sales_act ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Asset account:</label>
                    <select name="inventory_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('inventory_account', $selected_category->dflt_inventory_act ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation cost account:</label>
                    <select name="cogs_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('cogs_account', $selected_category->dflt_cogs_act ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Depreciation/Disposal account:</label>
                    <select name="adjustment_account"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">None</option>
                        @foreach($gl_accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('adjustment_account', $selected_category->dflt_adjustment_act ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                @if($selected_id != -1)
                    <button type="submit" name="UPDATE_ITEM" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                    <button type="submit" name="cancel" value="1"
                            class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                @else
                    <button type="submit" name="ADD_ITEM" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New</button>
                @endif
            </div>
        </div>
    </form>
@endsection