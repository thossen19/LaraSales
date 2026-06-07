@extends('layouts.app')

@section('title', 'Work Order Entry - Manufacturing')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Work Order Entry</h2>
    </div>

    @if($msg)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('manufacturing.work-order-entry') }}">
        @csrf
        <div class="bg-white shadow rounded-lg p-6 max-w-2xl">
            @if($workOrder)
                <input type="hidden" name="selected_id" value="{{ $workOrder->id }}">
                <input type="hidden" name="wo_ref" value="{{ $workOrder->wo_ref }}">
                <input type="hidden" name="units_issued" value="{{ $workOrder->units_issued }}">
                <input type="hidden" name="released" value="{{ $workOrder->released ? 1 : 0 }}">
                <input type="hidden" name="released_date" value="{{ $workOrder->released_date ? \Carbon\Carbon::parse($workOrder->released_date)->format('Y-m-d') : '' }}">

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reference:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $workOrder->wo_ref }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $wo_types[$workOrder->type] ?? 'Unknown' }}</p>
                        <input type="hidden" name="type" value="{{ $workOrder->type }}">
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reference:</label>
                        <input type="text" name="wo_ref" value="{{ old('wo_ref', $next_ref) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type:</label>
                        <select name="type" onchange="this.form.submit()"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($wo_types as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $workOrder->type ?? 0) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            @if($workOrder && $workOrder->released)
                <input type="hidden" name="stock_id" value="{{ $workOrder->stock_id }}">
                <input type="hidden" name="StockLocation" value="{{ $workOrder->loc_code }}">
                <input type="hidden" name="type" value="{{ $workOrder->type }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Item:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $workOrder->stock_id }} - {{ $workOrder->item->name ?? '' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Destination Location:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $workOrder->location->location_name ?? $workOrder->loc_code }}</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Item:</label>
                        <select name="stock_id"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Item</option>
                            @foreach($manufactured_items as $item)
                                <option value="{{ $item->code }}" {{ old('stock_id', $workOrder->stock_id ?? '') == $item->code ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Destination Location:</label>
                        <select name="StockLocation"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Location</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->loc_code }}" {{ old('StockLocation', $workOrder->loc_code ?? '') == $loc->loc_code ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            @php
                $type = old('type', $workOrder->type ?? 0);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                @if($type == 2)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity Required:</label>
                        <input type="number" name="quantity" step="any" min="0"
                               value="{{ old('quantity', $workOrder->units_reqd ?? 1) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @if($workOrder && $workOrder->released)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity Manufactured:</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ number_format($workOrder->units_issued, 2) }}</p>
                        </div>
                    @endif
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity:</label>
                        <input type="number" name="quantity" step="any" min="0"
                               value="{{ old('quantity', $workOrder->units_reqd ?? 1) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date:</label>
                    <input type="date" name="date_"
                           value="{{ old('date_', $workOrder ? \Carbon\Carbon::parse($workOrder->date_)->format('Y-m-d') : date('Y-m-d')) }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                @if($type == 2)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date Required By:</label>
                        <input type="date" name="RequDate"
                               value="{{ old('RequDate', $workOrder && $workOrder->required_by ? \Carbon\Carbon::parse($workOrder->required_by)->format('Y-m-d') : '') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                @else
                    <input type="hidden" name="RequDate" value="">
                @endif
            </div>

            @if($type != 2)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Labour:</label>
                        <input type="number" name="Labour" step="any" min="0"
                               value="{{ old('Labour', $workOrder && $type == 0 ? $workOrder->labour_cost : ($type == 1 ? 0 : 0)) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Credit Labour Account:</label>
                        <select name="cr_lab_acc"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Account</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ old('cr_lab_acc', $workOrder->cr_lab_acc ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Overhead:</label>
                        <input type="number" name="Costs" step="any" min="0"
                               value="{{ old('Costs', $workOrder && $type == 0 ? $workOrder->additional_costs : ($type == 1 ? 0 : 0)) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Credit Overhead Account:</label>
                        <select name="cr_acc"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Account</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ old('cr_acc', $workOrder->cr_acc ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            @if($workOrder && $workOrder->released && $workOrder->released_date)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Released On:</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($workOrder->released_date)->format('d/m/Y') }}</p>
                </div>
            @endif

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Memo:</label>
                <textarea name="memo_" rows="4"
                          class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('memo_', $workOrder->memo ?? '') }}</textarea>
            </div>

            <div class="mt-6 flex gap-2">
                @if($workOrder)
                    <button type="submit" name="UPDATE_ITEM" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                    @if($workOrder->released)
                        <button type="submit" name="close" value="1"
                                class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
                                onclick="return confirm('Are you sure you want to close this work order?')">Close This Work Order</button>
                    @endif
                    <button type="submit" name="delete" value="1"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                            onclick="return confirm('Are you sure you want to delete this work order?')">Delete This Work Order</button>
                @else
                    <button type="submit" name="ADD_ITEM" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Workorder</button>
                @endif
            </div>
        </div>
    </form>
@endsection