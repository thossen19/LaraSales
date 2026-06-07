@extends('layouts.app')

@section('title', 'Enter Supplier Invoice - Sales ERP')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Enter Supplier Invoice</h2>
        <p class="mt-2 text-gray-600">Record a supplier invoice against received goods or GL entries.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('purchases.invoices.index') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="supplier_id" value="{{ $cart['supplier_id'] ?? '' }}">
        <input type="hidden" name="update" value="1">

        <!-- Invoice Header -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Invoice Header
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ ($cart['supplier_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date <span class="text-red-500">*</span></label>
                        <input type="date" name="tran_date" value="{{ $cart['tran_date'] ?? date('Y-m-d') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ $cart['due_date'] ?? '' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                        <input type="text" name="reference" value="{{ $cart['reference'] ?? '' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Reference</label>
                        <input type="text" name="supp_reference" value="{{ $cart['supp_reference'] ?? '' }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dimension</label>
                        <select name="dimension_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="0">None</option>
                            @foreach($dimensions as $dim)
                                <option value="{{ $dim->id }}" {{ ($cart['dimension_id'] ?? 0) == $dim->id ? 'selected' : '' }}>{{ $dim->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($cart['supplier_id'])
        <!-- Outstanding GRN Items -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Goods Received Note Items Not Invoiced
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-600">Select GRN items to include on this invoice.</p>
                    <button type="submit" name="InvGRNAll" value="1"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add All Items
                    </button>
                </div>
                @if(count($outstanding_grn_items) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty Received</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Prev Invoiced</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">This Invoice Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Select</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($outstanding_grn_items as $item)
                                    @php $id = $item['id']; @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['item']['code'] ?? '' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item['description'] ?? '' }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item['received_quantity'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item['invoiced_quantity'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium text-indigo-700">{{ max(0, ($item['received_quantity'] ?? 0) - ($item['invoiced_quantity'] ?? 0)) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="submit" name="grn_item_id{{ $id }}" value="{{ $id }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Add
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <p class="text-sm">There are no items from GRNs for this supplier to invoice.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if($cart['supplier_id'] && !empty($cart['grn_items']))
        <!-- Invoiced Items -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Items Received Charged on this Invoice
                </h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Code</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Line Total</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Remove</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cart['grn_items'] as $id => $grn_item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $grn_item['stock_id'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $grn_item['item_description'] }}</td>
                                <td class="px-4 py-3">
                                    <input type="number" step="any" min="0"
                                           name="this_quantity_inv{{ $id }}"
                                           value="{{ $grn_item['this_quantity_inv'] ?? 0 }}"
                                           class="w-24 text-right rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="any" min="0"
                                           name="ChgPrice{{ $id }}"
                                           value="{{ $grn_item['chg_price'] ?? 0 }}"
                                           class="w-28 text-right rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format(($grn_item['this_quantity_inv'] ?? 0) * ($grn_item['chg_price'] ?? 0), 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button type="submit" name="Delete{{ $id }}" value="{{ $id }}"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition"
                                            onclick="return confirm('Remove this item from the invoice?')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex items-center justify-end">
                    <button type="submit" name="update" value="1"
                            class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- GL Code Items -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    GL Code Items
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GL Code</label>
                        <input type="text" name="gl_code"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               placeholder="e.g. 5000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dimension</label>
                        <select name="gl_dimension_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="0">None</option>
                            @foreach($dimensions as $dim)
                                <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" step="any" name="gl_amount" value="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Memo</label>
                        <input type="text" name="gl_memo"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <button type="submit" name="AddGLCodeToTrans" value="1"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm w-full justify-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add to Invoice
                        </button>
                    </div>
                </div>

                @if(!empty($cart['gl_items']))
                <div class="overflow-x-auto mt-6">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">GL Code</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Memo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Remove</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cart['gl_items'] as $gl_idx => $gl_item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $gl_item['gl_code'] }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ number_format($gl_item['amount'], 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $gl_item['memo'] ?? '' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button type="submit" name="Delete2{{ $gl_idx }}" value="{{ $gl_idx }}"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition"
                                            onclick="return confirm('Remove this GL entry?')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <!-- Memo & Totals -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    Memo & Totals
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Memo</label>
                        <textarea name="comments" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                  placeholder="Optional notes...">{{ $cart['comments'] ?? '' }}</textarea>
                    </div>
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 flex flex-col justify-center">
                        <div class="flex justify-between text-sm py-1">
                            <span class="font-medium text-gray-600">Total Direct:</span>
                            <span class="font-semibold text-gray-900">{{ number_format($ov_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold border-t border-gray-200 pt-2 mt-1">
                            <span class="text-gray-800">Total Amount:</span>
                            <span class="text-indigo-700 text-base">{{ number_format($ov_amount ?? 0, 2) }} ({{ $cart['curr_code'] ?? 'USD' }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center gap-4">
            <a href="{{ route('purchases.index') }}"
               class="inline-flex items-center px-6 py-2.5 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition border border-gray-300 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Cancel
            </a>
            <button type="submit" name="PostInvoice" value="1"
                    class="inline-flex items-center px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Enter Invoice
            </button>
        </div>
    </form>
@endsection
