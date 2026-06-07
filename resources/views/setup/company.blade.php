@extends('layouts.app')
@section('title', 'Company Setup - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Company Setup</h2>
    <p class="mt-2 text-gray-600">Company accounts settings and parameters, default values and preferences.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('setup.company') }}" enctype="multipart/form-data" class="bg-white shadow rounded-lg">
    @csrf
    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Column: General settings -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">General settings</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name (to appear on reports) *</label>
                    <input type="text" name="coy_name" value="{{ old('coy_name', $settings->get('coy_name')?->value ?? $company?->name ?? 'My Company') }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="postal_address" rows="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('postal_address', $settings->get('postal_address')?->value ?? $company?->address ?? "123 Business Street\nIndustrial Area\nCity, State 400001") }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domicile</label>
                    <input type="text" name="domicile" value="{{ old('domicile', $settings->get('domicile')?->value ?? 'India') }}" maxlength="55" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->get('phone')?->value ?? $company?->phone ?? '+91 22 25764279') }}" maxlength="55" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fax Number</label>
                    <input type="text" name="fax" value="{{ old('fax', $settings->get('fax')?->value ?? '') }}" maxlength="55" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $settings->get('email')?->value ?? $company?->email ?? 'info@mycompany.com') }}" maxlength="55" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">BCC Address for all outgoing mails</label>
                    <input type="email" name="bcc_email" value="{{ old('bcc_email', $settings->get('bcc_email')?->value ?? 'bcc@mycompany.com') }}" maxlength="55" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Official Company Number</label>
                    <input type="text" name="coy_no" value="{{ old('coy_no', $settings->get('coy_no')?->value ?? $company?->registration_number ?? 'ABC-12345') }}" maxlength="25" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GSTNo</label>
                    <input type="text" name="gst_no" value="{{ old('gst_no', $settings->get('gst_no')?->value ?? $company?->tax_id ?? '37AAAPP2678Q1ZP') }}" maxlength="25" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Home Currency</label>
                    <select name="curr_default" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="USD" {{ old('curr_default', $settings->get('curr_default')?->value ?? 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="EUR" {{ old('curr_default') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="GBP" {{ old('curr_default') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        <option value="INR" {{ old('curr_default') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                        <option value="AUD" {{ old('curr_default') == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                        <option value="CAD" {{ old('curr_default') == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                        <option value="SGD" {{ old('curr_default') == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar</option>
                        <option value="MYR" {{ old('curr_default') == 'MYR' ? 'selected' : '' }}>MYR - Malaysian Ringgit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                    <p class="text-sm text-gray-500 mb-2">logo_frontaccounting.jpg</p>
                    <input type="file" name="pic" accept=".jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <label class="flex items-center mt-2">
                        <input type="checkbox" name="del_coy_logo" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Delete Company Logo</span>
                    </label>
                </div>

                <div class="space-y-3 pt-2">
                    @php
                        $checks = ['time_zone', 'company_logo_report', 'barcodes_on_stock', 'ref_no_auto_increase',
                            'dim_on_recurrent_invoice', 'long_description_invoice', 'company_logo_on_views'];
                        $checkLabels = [
                            'time_zone' => 'Time Zone on Reports',
                            'company_logo_report' => 'Company Logo on Reports',
                            'barcodes_on_stock' => 'Use Barcodes on Stocks',
                            'ref_no_auto_increase' => 'Auto Increase of Document References',
                            'dim_on_recurrent_invoice' => 'Use Dimensions on Recurrent Invoices',
                            'long_description_invoice' => 'Use Long Descriptions on Invoices',
                            'company_logo_on_views' => 'Company Logo on Views',
                        ];
                    @endphp
                    @foreach($checks as $chk)
                    <label class="flex items-center">
                        <input type="checkbox" name="{{ $chk }}" value="1" {{ old($chk, $settings->get($chk)?->value) === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $checkLabels[$chk] }}</span>
                    </label>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Scheme Version</label>
                    <p class="text-sm text-gray-500 py-2 px-3 bg-gray-50 rounded-md">2.4.1</p>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- General Ledger Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">General Ledger Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year</label>
                        <select name="f_year" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="2025-2026" {{ old('f_year', $settings->get('f_year')?->value ?? '2025-2026') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                            <option value="2026-2027" {{ old('f_year', $settings->get('f_year')?->value) == '2026-2027' ? 'selected' : '' }}>2026-2027</option>
                            <option value="2027-2028" {{ old('f_year', $settings->get('f_year')?->value) == '2027-2028' ? 'selected' : '' }}>2027-2028</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Periods</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="tax_prd" value="{{ old('tax_prd', $settings->get('tax_prd')?->value ?? '12') }}" min="1" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-gray-500">Months.</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Last Period</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="tax_last" value="{{ old('tax_last', $settings->get('tax_last')?->value ?? '12') }}" min="1" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-gray-500">Months back.</span>
                        </div>
                    </div>

                    @php
                        $glChecks = ['alternative_tax_include_on_docs', 'suppress_tax_rates', 'auto_curr_reval'];
                        $glLabels = [
                            'alternative_tax_include_on_docs' => 'Put alternative Tax Include on Docs',
                            'suppress_tax_rates' => 'Suppress Tax Rates on Docs',
                            'auto_curr_reval' => 'Automatic Revaluation Currency Accounts',
                        ];
                    @endphp
                    @foreach($glChecks as $chk)
                    <label class="flex items-center">
                        <input type="checkbox" name="{{ $chk }}" value="1" {{ old($chk, $settings->get($chk)?->value ?? ($chk === 'auto_curr_reval' ? '1' : '0')) === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $glLabels[$chk] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Sales Pricing -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Sales Pricing</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Base for auto price calculations</label>
                        <select name="base_sales" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">No base price list</option>
                            @foreach($salesTypes ?? [] as $type)
                                <option value="{{ $type->id }}" {{ old('base_sales', $settings->get('base_sales')?->value) == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Add Price from Std Cost</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="add_pct" value="{{ old('add_pct', $settings->get('add_pct')?->value ?? '') }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-gray-500">%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Round calculated prices to nearest</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="round_to" value="{{ old('round_to', $settings->get('round_to')?->value ?? '1') }}" min="1" step="0.01" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-gray-500">cents</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optional Modules -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Optional Modules</h3>
                <div class="space-y-4">
                    @php
                        $modChecks = ['use_manufacturing', 'use_fixed_assets'];
                        $modLabels = ['use_manufacturing' => 'Manufacturing', 'use_fixed_assets' => 'Fixed Assets'];
                    @endphp
                    @foreach($modChecks as $chk)
                    <label class="flex items-center">
                        <input type="checkbox" name="{{ $chk }}" value="1" {{ old($chk, $settings->get($chk)?->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $modLabels[$chk] }}</span>
                    </label>
                    @endforeach

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Use Dimensions</label>
                        <select name="use_dimension" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ old('use_dimension', $settings->get('use_dimension')?->value ?? '0') == '0' ? 'selected' : '' }}>0 - No dimensions</option>
                            <option value="1" {{ old('use_dimension', $settings->get('use_dimension')?->value) == '1' ? 'selected' : '' }}>1 - Use 1 dimension</option>
                            <option value="2" {{ old('use_dimension', $settings->get('use_dimension')?->value) == '2' ? 'selected' : '' }}>2 - Use 2 dimensions</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- User Interface Options -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">User Interface Options</h3>
                <div class="space-y-4">
                    @php
                        $uiChecks = ['shortname_name_in_list', 'print_dialog_direct', 'no_item_list', 'no_customer_list', 'no_supplier_list'];
                        $uiLabels = [
                            'shortname_name_in_list' => 'Short Name and Name in List',
                            'print_dialog_direct' => 'Open Print Dialog Direct on Reports',
                            'no_item_list' => 'Search Item List',
                            'no_customer_list' => 'Search Customer List',
                            'no_supplier_list' => 'Search Supplier List',
                        ];
                    @endphp
                    @foreach($uiChecks as $chk)
                    <label class="flex items-center">
                        <input type="checkbox" name="{{ $chk }}" value="1" {{ old($chk, $settings->get($chk)?->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $uiLabels[$chk] }}</span>
                    </label>
                    @endforeach

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Login Timeout</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="login_tout" value="{{ old('login_tout', $settings->get('login_tout')?->value ?? '3600') }}" min="10" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-gray-500">seconds</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max day range in documents</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="max_days_in_docs" value="{{ old('max_days_in_docs', $settings->get('max_days_in_docs')?->value ?? '180') }}" min="1" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="text-sm text-gray-500">days.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        <button type="submit" name="update" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            Update
        </button>
    </div>
</form>
@endsection