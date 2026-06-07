@extends('layouts.app')
@section('title', 'System and General GL Setup - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">System and General GL Setup</h2>
    <p class="mt-2 text-gray-600">Configure system and GL settings.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.system-gl') }}">
    @csrf
    <div class="bg-white shadow rounded-lg p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Left Column -->
        <div class="space-y-6">

            <!-- General GL -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">General GL</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Past Due Days Interval:</label>
                        <input type="number" name="past_due_days" value="{{ $prefs['past_due_days'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-500">days</span>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Accounts Type:</label>
                        <select name="accounts_alpha" class="w-48 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($acc_types as $val => $label)
                                <option value="{{ $val }}" {{ $prefs['accounts_alpha'] == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Retained Earnings:</label>
                        <select name="retained_earnings_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['retained_earnings_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profit/Loss Year:</label>
                        <select name="profit_loss_year_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['profit_loss_year_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exchange Variances Account:</label>
                        <select name="exchange_diff_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['exchange_diff_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Charges Account:</label>
                        <select name="bank_charge_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['bank_charge_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Tax Algorithm:</label>
                        <select name="tax_algorithm" class="w-48 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($tax_algorithms as $val => $label)
                                <option value="{{ $val }}" {{ $prefs['tax_algorithm'] == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dimension Defaults -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Dimension Defaults</h3>
                <div class="flex items-center">
                    <label class="block text-sm font-medium text-gray-700 w-60">Dimension Required By After:</label>
                    <input type="number" name="default_dim_required" value="{{ $prefs['default_dim_required'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-500">days</span>
                </div>
            </div>

            <!-- Customers and Sales -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Customers and Sales</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Default Credit Limit:</label>
                        <input type="text" name="default_credit_limit" value="{{ $prefs['default_credit_limit'] }}" class="w-32 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Invoice Identification:</label>
                        <select name="print_invoice_no" class="w-32 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="1" {{ $prefs['print_invoice_no'] == '1' ? 'selected' : '' }}>Number</option>
                            <option value="0" {{ $prefs['print_invoice_no'] == '0' ? 'selected' : '' }}>Reference</option>
                        </select>
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" name="accumulate_shipping" value="1" {{ $prefs['accumulate_shipping'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Accumulate batch shipping:</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="print_item_images_on_quote" value="1" {{ $prefs['print_item_images_on_quote'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Print Item Image on Quote:</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Legal Text on Invoice:</label>
                        <textarea name="legal_text" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $prefs['legal_text'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Charged Account:</label>
                        <select name="freight_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['freight_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deferred Income Account:</label>
                        <select name="deferred_income_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Not used</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['deferred_income_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Customers and Sales Defaults -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Customers and Sales Defaults</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receivable Account:</label>
                        <select name="debtors_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['debtors_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sales Account:</label>
                        <select name="default_sales_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_sales_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sales Discount Account:</label>
                        <select name="default_sales_discount_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_sales_discount_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prompt Payment Discount Account:</label>
                        <select name="default_prompt_payment_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_prompt_payment_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Quote Valid Days:</label>
                        <input type="number" name="default_quote_valid_days" value="{{ $prefs['default_quote_valid_days'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-500">days</span>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Delivery Required By:</label>
                        <input type="number" name="default_delivery_required" value="{{ $prefs['default_delivery_required'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-500">days</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">

            <!-- Suppliers and Purchasing -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Suppliers and Purchasing</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Delivery Over-Receive Allowance:</label>
                        <input type="text" name="po_over_receive" value="{{ $prefs['po_over_receive'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-500">%</span>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Invoice Over-Charge Allowance:</label>
                        <input type="text" name="po_over_charge" value="{{ $prefs['po_over_charge'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-500">%</span>
                    </div>
                </div>
            </div>

            <!-- Suppliers and Purchasing Defaults -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Suppliers and Purchasing Defaults</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payable Account:</label>
                        <select name="creditors_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['creditors_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Discount Account:</label>
                        <select name="pyt_discount_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['pyt_discount_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GRN Clearing Account:</label>
                        <select name="grn_clearing_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">No postings on GRN</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['grn_clearing_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Receival Required By:</label>
                        <input type="number" name="default_receival_required" value="{{ $prefs['default_receival_required'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-500">days</span>
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" name="show_po_item_codes" value="1" {{ $prefs['show_po_item_codes'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Show PO item codes:</span>
                    </label>
                </div>
            </div>

            <!-- Inventory -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Inventory</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_negative_stock" value="1" {{ $prefs['allow_negative_stock'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Allow Negative Inventory:</span>
                    </label>
                    <p class="text-xs text-orange-600 -mt-2 ml-6">Warning: This may cause a delay in GL postings</p>
                    <label class="flex items-center">
                        <input type="checkbox" name="no_zero_lines_amount" value="1" {{ $prefs['no_zero_lines_amount'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">No zero-amounts (Service):</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="loc_notification" value="1" {{ $prefs['loc_notification'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Location Notifications:</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_negative_prices" value="1" {{ $prefs['allow_negative_prices'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Allow Negative Prices:</span>
                    </label>
                </div>
            </div>

            <!-- Items Defaults -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Items Defaults</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sales Account:</label>
                        <select name="default_inv_sales_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_inv_sales_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Account:</label>
                        <select name="default_inventory_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_inventory_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">C.O.G.S. Account:</label>
                        <select name="default_cogs_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_cogs_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Adjustments Account:</label>
                        <select name="default_adj_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_adj_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WIP Account:</label>
                        <select name="default_wip_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_wip_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fixed Assets Defaults -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Fixed Assets Defaults</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loss On Asset Disposal Account:</label>
                        <select name="default_loss_on_asset_disposal_act" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- None --</option>
                            @foreach($gl_accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $prefs['default_loss_on_asset_disposal_act'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 w-60">Depreciation Period:</label>
                        <select name="depreciation_period" class="w-40 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ $prefs['depreciation_period'] == '0' ? 'selected' : '' }}>Monthly</option>
                            <option value="1" {{ $prefs['depreciation_period'] == '1' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Manufacturing Defaults -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Manufacturing Defaults</h3>
                <div class="flex items-center">
                    <label class="block text-sm font-medium text-gray-700 w-60">Work Order Required By After:</label>
                    <input type="number" name="default_workorder_required" value="{{ $prefs['default_workorder_required'] }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-500">days</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 px-6 py-4 bg-gray-50 rounded-lg border border-gray-200">
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            Update
        </button>
    </div>
</form>
@endsection