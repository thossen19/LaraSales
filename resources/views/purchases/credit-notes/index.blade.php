@extends('layouts.app')

@section('title', 'Supplier Credit Note - Sales ERP')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Supplier Credit Note</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('purchases.credit-notes.index') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="supplier_id" value="{{ $cart['supplier_id'] ?? '' }}">
        <input type="hidden" name="update" value="1">

        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <select name="supplier_id" onchange="this.form.submit()"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ ($cart['supplier_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Credit Note Date</label>
                    <input type="date" name="tran_date" value="{{ $cart['tran_date'] ?? date('Y-m-d') }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="date" name="due_date" value="{{ $cart['due_date'] ?? '' }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference</label>
                    <input type="text" name="reference" value="{{ $cart['reference'] ?? '' }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supplier's Reference</label>
                    <input type="text" name="supp_reference" value="{{ $cart['supp_reference'] ?? '' }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dimension</label>
                    <select name="dimension_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="0">None</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}" {{ ($cart['dimension_id'] ?? 0) == $dim->id ? 'selected' : '' }}>{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dimension 2</label>
                    <select name="dimension2_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="0">None</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}" {{ ($cart['dimension2_id'] ?? 0) == $dim->id ? 'selected' : '' }}>{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($cart['supplier_id'])
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Outstanding GRN Items That Can Be Credited</h3>
                <button type="submit" name="AddAll" value="1"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add All Items</button>
            </div>
            @if(count($creditable_grn_items) > 0)
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left px-3 py-2">Item Code</th>
                            <th class="text-left px-3 py-2">Description</th>
                            <th class="text-right px-3 py-2">Qty Received</th>
                            <th class="text-right px-3 py-2">Prev Credited</th>
                            <th class="text-right px-3 py-2">This Credit</th>
                            <th class="text-center px-3 py-2">Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditable_grn_items as $item)
                            @php $id = $item['id']; @endphp
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $item['item']['code'] ?? '' }}</td>
                                <td class="px-3 py-2">{{ $item['description'] ?? '' }}</td>
                                <td class="text-right px-3 py-2">{{ $item['received_quantity'] ?? 0 }}</td>
                                <td class="text-right px-3 py-2">{{ $item['credited_quantity'] ?? 0 }}</td>
                                <td class="text-right px-3 py-2">{{ max(0, ($item['received_quantity'] ?? 0) - ($item['credited_quantity'] ?? 0)) }}</td>
                                <td class="text-center px-3 py-2">
                                    <button type="submit" name="grn_item_id{{ $id }}" value="{{ $id }}"
                                            class="text-blue-600 hover:text-blue-800">Add</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm">There are no items from GRNs for this supplier to credit.</p>
            @endif
        </div>
        @endif

        @if($cart['supplier_id'] && !empty($cart['grn_items']))
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Items Credited on this Credit Note</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-3 py-2">Item Code</th>
                        <th class="text-left px-3 py-2">Description</th>
                        <th class="text-right px-3 py-2">Qty</th>
                        <th class="text-right px-3 py-2">Unit Price</th>
                        <th class="text-right px-3 py-2">Line Total</th>
                        <th class="text-center px-3 py-2">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart['grn_items'] as $id => $grn_item)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $grn_item['stock_id'] }}</td>
                        <td class="px-3 py-2">{{ $grn_item['item_description'] }}</td>
                        <td class="px-3 py-2">
                            <input type="number" step="any" min="0"
                                   name="this_quantity_cn{{ $id }}"
                                   value="{{ $grn_item['this_quantity_cn'] ?? 0 }}"
                                   class="w-20 text-right rounded border-gray-300">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="any" min="0"
                                   name="ChgPrice{{ $id }}"
                                   value="{{ $grn_item['chg_price'] ?? 0 }}"
                                   class="w-24 text-right rounded border-gray-300">
                        </td>
                        <td class="text-right px-3 py-2">{{ number_format(($grn_item['this_quantity_cn'] ?? 0) * ($grn_item['chg_price'] ?? 0), 2) }}</td>
                        <td class="text-center px-3 py-2">
                            <button type="submit" name="Delete{{ $id }}" value="{{ $id }}"
                                    class="text-red-600 hover:text-red-800">Remove</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 flex items-center justify-end">
                <button type="submit" name="update" value="1"
                        class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Refresh</button>
            </div>
        </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">GL Code Items</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">GL Code</label>
                    <input type="text" name="gl_code"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="e.g. 5000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dimension</label>
                    <select name="gl_dimension_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="0">None</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dimension 2</label>
                    <select name="gl_dimension2_id"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="0">None</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" step="any" name="gl_amount" value="0"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Memo</label>
                    <input type="text" name="gl_memo"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit" name="AddGLCode" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Add to Credit Note</button>
                </div>
            </div>

            @if(!empty($cart['gl_items']))
            <table class="w-full text-sm mt-4">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-3 py-2">GL Code</th>
                        <th class="text-left px-3 py-2">Amount</th>
                        <th class="text-left px-3 py-2">Memo</th>
                        <th class="text-center px-3 py-2">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart['gl_items'] as $gl_idx => $gl_item)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $gl_item['gl_code'] }}</td>
                        <td class="px-3 py-2">{{ number_format($gl_item['amount'], 2) }}</td>
                        <td class="px-3 py-2">{{ $gl_item['memo'] ?? '' }}</td>
                        <td class="text-center px-3 py-2">
                            <button type="submit" name="Delete2{{ $gl_idx }}" value="{{ $gl_idx }}"
                                    class="text-red-600 hover:text-red-800">Remove</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Memo</label>
                    <textarea name="comments" rows="3"
                              class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $cart['comments'] ?? '' }}</textarea>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium">Total Direct:</span>
                        <span>{{ number_format($ov_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold border-t pt-2">
                        <span>Total Amount:</span>
                        <span>{{ number_format($ov_amount ?? 0, 2) }} ({{ $cart['curr_code'] ?? 'USD' }})</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('purchases.index') }}"
               class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Cancel</a>
            <button type="submit" name="ProcessCreditNote" value="1"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Process Credit Note</button>
        </div>
    </form>
@endsection
