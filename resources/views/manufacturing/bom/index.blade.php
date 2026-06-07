@extends('layouts.app')

@section('title', 'Bill Of Materials - Manufacturing')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Bill Of Materials</h2>
    </div>

    @if($msg)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('manufacturing.bom.index') }}">
        @csrf
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select a manufacturable item:</label>
                    <select name="stock_id" onchange="this.form.submit()"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Item</option>
                        @foreach($manufactured_items as $item)
                            <option value="{{ $item->code }}" {{ $stock_id == $item->code ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    @if($stock_id && $bom_items->count() > 0)
                        <label class="block text-sm font-medium text-gray-700">Copy BOM to another manufacturable item:</label>
                        <select name="new_stock_id" onchange="this.form.submit()"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Item</option>
                            @foreach($manufactured_items as $item)
                                @if($item->code != $stock_id)
                                    <option value="{{ $item->code }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>
    </form>

    @if($stock_id)
    <form method="POST" action="{{ route('manufacturing.bom.index') }}">
        @csrf
        <input type="hidden" name="stock_id" value="{{ $stock_id }}">

        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">Code</th>
                        <th class="text-left px-4 py-3">Description</th>
                        <th class="text-left px-4 py-3">Location</th>
                        <th class="text-left px-4 py-3">Work Centre</th>
                        <th class="text-right px-4 py-3">Quantity</th>
                        <th class="text-left px-4 py-3">Units</th>
                        <th class="text-center px-4 py-3" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bom_items as $bom)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $bom->component }}</td>
                        <td class="px-4 py-3">{{ $bom->description }}</td>
                        <td class="px-4 py-3">{{ $bom->location_name }}</td>
                        <td class="px-4 py-3">{{ $bom->WorkCentreDescription }}</td>
                        <td class="text-right px-4 py-3">{{ $bom->quantity }}</td>
                        <td class="px-4 py-3">{{ $bom->units }}</td>
                        <td class="text-center px-4 py-3">
                            <button type="submit" name="Edit{{ $bom->id }}" value="1"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                        </td>
                        <td class="text-center px-4 py-3">
                            <button type="submit" name="Delete{{ $bom->id }}" value="1"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                    onclick="return confirm('Are you sure you want to delete this component?')">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">No components defined for this item.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            @if($selected_id > 0)
                <input type="hidden" name="selected_id" value="{{ $selected_id }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($selected_id > 0 && $selected_component)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Component:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">
                            {{ $selected_component->component }} - {{ $selected_component->componentItem->name ?? '' }}
                        </p>
                        <input type="hidden" name="component" value="{{ $selected_component->component }}">
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Component:</label>
                        <select name="component"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Component</option>
                            @foreach($component_items as $item)
                                <option value="{{ $item->code }}" {{ old('component', $selected_component->component ?? '') == $item->code ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Location to Draw From:</label>
                    <select name="loc_code"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->loc_code }}" {{ old('loc_code', $selected_component->loc_code ?? '') == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Work Centre Added:</label>
                    <select name="workcentre_added"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Work Centre</option>
                        @foreach($work_centres as $wc)
                            <option value="{{ $wc->id }}" {{ old('workcentre_added', $selected_component->workcentre_added ?? '') == $wc->id ? 'selected' : '' }}>{{ $wc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity:</label>
                    <input type="number" name="quantity" step="any" min="0"
                           value="{{ old('quantity', $selected_component->quantity ?? 1) }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                @if($selected_id > 0)
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
    @endif
@endsection