@extends('layouts.app')
@section('title', 'Display Setup - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Display Setup</h2>
    <p class="mt-2 text-gray-600">Configure display preferences, date formats, decimal places and other user interface options.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('setup.display') }}" class="bg-white shadow rounded-lg">
    @csrf
    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Decimal Places -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Decimal Places</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prices/Amounts</label>
                        <select name="prices_dec" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(range(0,10) as $i)
                                <option value="{{ $i }}" {{ ($prefs['prices_dec'] ?? 2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantities</label>
                        <select name="qty_dec" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(range(0,10) as $i)
                                <option value="{{ $i }}" {{ ($prefs['qty_dec'] ?? 2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exchange Rates</label>
                        <select name="rates_dec" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(range(0,10) as $i)
                                <option value="{{ $i }}" {{ ($prefs['rates_dec'] ?? 4) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Percentages</label>
                        <select name="percent_dec" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(range(0,10) as $i)
                                <option value="{{ $i }}" {{ ($prefs['percent_dec'] ?? 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date Format and Separators -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Date Format and Separators</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                        <select name="date_format" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['date_format'] ?? 0) == 0 ? 'selected' : '' }}>dd/mm/yy</option>
                            <option value="1" {{ ($prefs['date_format'] ?? 0) == 1 ? 'selected' : '' }}>mm/dd/yy</option>
                            <option value="2" {{ ($prefs['date_format'] ?? 0) == 2 ? 'selected' : '' }}>dd.mm.yy</option>
                            <option value="3" {{ ($prefs['date_format'] ?? 0) == 3 ? 'selected' : '' }}>yyyy-mm-dd</option>
                            <option value="4" {{ ($prefs['date_format'] ?? 0) == 4 ? 'selected' : '' }}>yy/mm/dd</option>
                            <option value="5" {{ ($prefs['date_format'] ?? 0) == 5 ? 'selected' : '' }}>mm/dd/yyyy</option>
                            <option value="6" {{ ($prefs['date_format'] ?? 0) == 6 ? 'selected' : '' }}>dd/mm/yyyy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Separator</label>
                        <select name="date_sep" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['date_sep'] ?? 0) == 0 ? 'selected' : '' }}>/</option>
                            <option value="1" {{ ($prefs['date_sep'] ?? 0) == 1 ? 'selected' : '' }}>-</option>
                            <option value="2" {{ ($prefs['date_sep'] ?? 0) == 2 ? 'selected' : '' }}>.</option>
                            <option value="3" {{ ($prefs['date_sep'] ?? 0) == 3 ? 'selected' : '' }}> </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thousand Separator</label>
                        <select name="tho_sep" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['tho_sep'] ?? 0) == 0 ? 'selected' : '' }}>,</option>
                            <option value="1" {{ ($prefs['tho_sep'] ?? 0) == 1 ? 'selected' : '' }}>.</option>
                            <option value="2" {{ ($prefs['tho_sep'] ?? 0) == 2 ? 'selected' : '' }}> </option>
                            <option value="3" {{ ($prefs['tho_sep'] ?? 0) == 3 ? 'selected' : '' }}> (none)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Decimal Separator</label>
                        <select name="dec_sep" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['dec_sep'] ?? 0) == 0 ? 'selected' : '' }}>.</option>
                            <option value="1" {{ ($prefs['dec_sep'] ?? 0) == 1 ? 'selected' : '' }}>,</option>
                        </select>
                    </div>
                    <label class="flex items-center">
                        <input type="checkbox" name="use_date_picker" value="1" {{ ($prefs['use_date_picker'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Use Date Picker</span>
                    </label>
                </div>
            </div>

            <!-- Reports -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Reports</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Save Report Selection Days</label>
                        <input type="number" name="save_report_selections" value="{{ $prefs['save_report_selections'] ?? 0 }}" min="0" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Report Destination</label>
                        <select name="def_print_destination" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['def_print_destination'] ?? 0) == 0 ? 'selected' : '' }}>PDF/Printer</option>
                            <option value="1" {{ ($prefs['def_print_destination'] ?? 0) == 1 ? 'selected' : '' }}>Excel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Report Orientation</label>
                        <select name="def_print_orientation" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['def_print_orientation'] ?? 0) == 0 ? 'selected' : '' }}>Portrait</option>
                            <option value="1" {{ ($prefs['def_print_orientation'] ?? 0) == 1 ? 'selected' : '' }}>Landscape</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Miscellaneous -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Miscellaneous</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="show_hints" value="1" {{ ($prefs['show_hints'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Show hints for new users</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="show_gl" value="1" {{ ($prefs['show_gl'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Show GL Information</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="show_codes" value="1" {{ ($prefs['show_codes'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Show Item Codes</span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                        <select name="theme" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="default" {{ ($prefs['theme'] ?? 'default') == 'default' ? 'selected' : '' }}>Default</option>
                            <option value="dark" {{ ($prefs['theme'] ?? 'default') == 'dark' ? 'selected' : '' }}>Dark</option>
                            <option value="light" {{ ($prefs['theme'] ?? 'default') == 'light' ? 'selected' : '' }}>Light</option>
                            <option value="blue" {{ ($prefs['theme'] ?? 'default') == 'blue' ? 'selected' : '' }}>Blue</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page Size</label>
                        <select name="page_size" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="A4" {{ ($prefs['page_size'] ?? 'A4') == 'A4' ? 'selected' : '' }}>A4</option>
                            <option value="Letter" {{ ($prefs['page_size'] ?? 'A4') == 'Letter' ? 'selected' : '' }}>Letter</option>
                            <option value="Legal" {{ ($prefs['page_size'] ?? 'A4') == 'Legal' ? 'selected' : '' }}>Legal</option>
                            <option value="A3" {{ ($prefs['page_size'] ?? 'A4') == 'A3' ? 'selected' : '' }}>A3</option>
                            <option value="A5" {{ ($prefs['page_size'] ?? 'A4') == 'A5' ? 'selected' : '' }}>A5</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start-up Tab</label>
                        <select name="startup_tab" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['startup_tab'] ?? 0) == 0 ? 'selected' : '' }}>Dashboard</option>
                            <option value="1" {{ ($prefs['startup_tab'] ?? 0) == 1 ? 'selected' : '' }}>Sales</option>
                            <option value="2" {{ ($prefs['startup_tab'] ?? 0) == 2 ? 'selected' : '' }}>Purchases</option>
                            <option value="3" {{ ($prefs['startup_tab'] ?? 0) == 3 ? 'selected' : '' }}>Inventory</option>
                            <option value="4" {{ ($prefs['startup_tab'] ?? 0) == 4 ? 'selected' : '' }}>Manufacturing</option>
                            <option value="5" {{ ($prefs['startup_tab'] ?? 0) == 5 ? 'selected' : '' }}>Banking</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Printing profile</label>
                        <select name="print_profile" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ ($prefs['print_profile'] ?? 0) == 0 ? 'selected' : '' }}>Browser printing support</option>
                            <option value="1" {{ ($prefs['print_profile'] ?? 0) == 1 ? 'selected' : '' }}>PDF</option>
                        </select>
                    </div>

                    <label class="flex items-center">
                        <input type="checkbox" name="rep_popup" value="1" {{ ($prefs['rep_popup'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Use popup window to display reports</span>
                    </label>
                    <p class="text-xs text-gray-500 -mt-2 ml-6">Set this option to on if your browser directly supports pdf files</p>

                    <label class="flex items-center">
                        <input type="checkbox" name="graphic_links" value="1" {{ ($prefs['graphic_links'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Use icons instead of text links</span>
                    </label>
                    <p class="text-xs text-gray-500 -mt-2 ml-6">Set this option to on for using icons instead of text links</p>

                    <label class="flex items-center">
                        <input type="checkbox" name="sticky_doc_date" value="1" {{ ($prefs['sticky_doc_date'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Remember last document date</span>
                    </label>
                    <p class="text-xs text-gray-500 -mt-2 ml-6">If set document date is remembered on subsequent documents, otherwise default is current date</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Query page size</label>
                        <input type="number" name="query_size" value="{{ $prefs['query_size'] ?? 10 }}" min="1" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction days</label>
                        <input type="number" name="transaction_days" value="{{ $prefs['transaction_days'] ?? 30 }}" min="1" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Language -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Language</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                        <select name="language" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="en_US" {{ ($prefs['language'] ?? 'en_US') == 'en_US' ? 'selected' : '' }}>English</option>
                            <option value="fr_FR" {{ ($prefs['language'] ?? 'en_US') == 'fr_FR' ? 'selected' : '' }}>French</option>
                            <option value="de_DE" {{ ($prefs['language'] ?? 'en_US') == 'de_DE' ? 'selected' : '' }}>German</option>
                            <option value="es_ES" {{ ($prefs['language'] ?? 'en_US') == 'es_ES' ? 'selected' : '' }}>Spanish</option>
                            <option value="ar_SA" {{ ($prefs['language'] ?? 'en_US') == 'ar_SA' ? 'selected' : '' }}>Arabic</option>
                            <option value="hi_IN" {{ ($prefs['language'] ?? 'en_US') == 'hi_IN' ? 'selected' : '' }}>Hindi</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        <button type="submit" name="setprefs" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            Update
        </button>
    </div>
</form>
@endsection