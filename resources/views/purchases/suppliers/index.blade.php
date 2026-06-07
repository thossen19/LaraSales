@extends('layouts.app')
@section('title', 'Suppliers - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Suppliers</h2>
    <p class="mt-2 text-gray-600">Manage your supplier database and vendor relationships.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('purchases.suppliers.index') }}" class="bg-white shadow rounded-lg p-4 mb-6">
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Select a supplier:</label>
            <select name="supplier_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">-- New supplier --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ $supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="pt-5">
            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2">Show inactive:</span>
            </label>
        </div>
    </div>
</form>

<form method="POST" action="{{ route('purchases.suppliers.index') }}">
@csrf
<input type="hidden" name="supplier_id" value="{{ $supplier_id }}">
<input type="hidden" name="show_inactive" value="{{ $show_inactive ? '1' : '' }}">

<!-- Tabs -->
<div x-data="{ tab: 'settings' }" class="mb-6">
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8">
            <button type="button" @click="tab = 'settings'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'settings', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'settings' }" class="px-1 py-4 border-b-2 font-medium text-sm focus:outline-none">
                General settings
            </button>
            <button type="button" @click="tab = 'contacts'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'contacts', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'contacts' }" class="px-1 py-4 border-b-2 font-medium text-sm focus:outline-none">
                Contacts
            </button>
            <button type="button" @click="tab = 'transactions'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'transactions', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'transactions' }" class="px-1 py-4 border-b-2 font-medium text-sm focus:outline-none">
                Transactions
            </button>
            <button type="button" @click="tab = 'orders'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'orders', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'orders' }" class="px-1 py-4 border-b-2 font-medium text-sm focus:outline-none">
                Purchase Orders
            </button>
            <button type="button" @click="tab = 'attachments'" :class="{ 'border-indigo-500 text-indigo-600': tab === 'attachments', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'attachments' }" class="px-1 py-4 border-b-2 font-medium text-sm focus:outline-none">
                Attachments
            </button>
        </nav>
    </div>

    <!-- Settings Tab -->
    <div x-show="tab === 'settings'" class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-0">
            <!-- Left Column -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Basic Data</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name:</label>
                        <input type="text" name="supp_name" value="{{ $form['supp_name'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Short Name:</label>
                        <input type="text" name="supp_ref" value="{{ $form['supp_ref'] }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GSTNo:</label>
                        <input type="text" name="gst_no" value="{{ $form['gst_no'] }}" maxlength="40" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website:</label>
                        <input type="text" name="website" value="{{ $form['website'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier's Currency:</label>
                        <select name="curr_code" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            @foreach($currencies as $c)
                                <option value="{{ $c->curr_abrev }}" {{ $form['curr_code'] == $c->curr_abrev ? 'selected' : '' }}>{{ $c->curr_abrev }} - {{ $c->currency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Group:</label>
                        <select name="tax_group_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach($tax_groups as $tg)
                                <option value="{{ $tg->id }}" {{ $form['tax_group_id'] == $tg->id ? 'selected' : '' }}>{{ $tg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Our Customer No:</label>
                        <input type="text" name="supp_account_no" value="{{ $form['supp_account_no'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                @if(!$edit_supplier)
                    <h4 class="text-md font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Contact Data</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person:</label>
                            <input type="text" name="contact_person" value="{{ $form['contact_person'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number:</label>
                            <input type="text" name="phone" value="{{ $form['phone'] }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone Number:</label>
                            <input type="text" name="phone2" value="{{ $form['phone2'] }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fax Number:</label>
                            <input type="text" name="fax" value="{{ $form['fax'] }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail:</label>
                            <input type="email" name="email" value="{{ $form['email'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Document Language:</label>
                            <select name="rep_lang" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="">-- System default --</option>
                                @foreach($languages as $code => $name)
                                    <option value="{{ $code }}" {{ $form['rep_lang'] == $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <h4 class="text-md font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Dimension</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 1:</label>
                        <select name="dimension_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="0">-- None --</option>
                            @foreach($dimensions as $d)
                                <option value="{{ $d->id }}" {{ $form['dimension_id'] == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dimension 2:</label>
                        <select name="dimension2_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="0">-- None --</option>
                            @foreach($dimensions as $d)
                                <option value="{{ $d->id }}" {{ $form['dimension2_id'] == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Purchasing</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name/Account:</label>
                        <input type="text" name="bank_account" value="{{ $form['bank_account'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Credit Limit:</label>
                        <input type="text" name="credit_limit" value="{{ $form['credit_limit'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms:</label>
                        <select name="payment_terms" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach($payment_terms as $pt)
                                <option value="{{ $pt->terms }}" {{ $form['payment_terms'] == $pt->terms ? 'selected' : '' }}>{{ $pt->terms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        @if(!$edit_supplier)
                            <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="tax_included" value="1" {{ $form['tax_included'] ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Prices contain tax included:</span>
                            </label>
                        @else
                            <input type="hidden" name="tax_included" value="{{ $form['tax_included'] ? '1' : '0' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prices contain tax included:</label>
                            <p class="text-sm text-gray-900">{{ $form['tax_included'] ? 'Yes' : 'No' }}</p>
                        @endif
                    </div>
                </div>

                <h4 class="text-md font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Accounts</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Accounts Payable Account:</label>
                        <select name="payable_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach(\App\Models\Account::orderBy('code')->get() as $acc)
                                <option value="{{ $acc->code }}" {{ $form['payable_account'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Account:</label>
                        <select name="purchase_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- Use Item Inventory/COGS Account --</option>
                            @foreach(\App\Models\Account::orderBy('code')->get() as $acc)
                                <option value="{{ $acc->code }}" {{ $form['purchase_account'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Discount Account:</label>
                        <select name="payment_discount_account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach(\App\Models\Account::orderBy('code')->get() as $acc)
                                <option value="{{ $acc->code }}" {{ $form['payment_discount_account'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($edit_supplier)
                    <h4 class="text-md font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Contact Data</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person:</label>
                            <input type="text" name="contact_person" value="{{ $form['contact_person'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number:</label>
                            <input type="text" name="phone" value="{{ $form['phone'] }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone Number:</label>
                            <input type="text" name="phone2" value="{{ $form['phone2'] }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fax Number:</label>
                            <input type="text" name="fax" value="{{ $form['fax'] }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail:</label>
                            <input type="email" name="email" value="{{ $form['email'] }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Document Language:</label>
                            <select name="rep_lang" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="">-- System default --</option>
                                @foreach($languages as $code => $name)
                                    <option value="{{ $code }}" {{ $form['rep_lang'] == $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Full Width Sections -->
        <div class="mt-8 space-y-6">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Addresses</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mailing Address:</label>
                        <textarea name="address" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ $form['address'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Physical Address:</label>
                        <textarea name="physical_address" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ $form['physical_address'] }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">General</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">General Notes:</label>
                        <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ $form['notes'] }}</textarea>
                    </div>
                    @if($edit_supplier)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier status:</label>
                            <select name="inactive" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="0" {{ !$form['inactive'] ? 'selected' : '' }}>Active</option>
                                <option value="1" {{ $form['inactive'] ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-4 mt-6 border-t border-gray-200 text-center">
            @if($edit_supplier)
                <button type="submit" name="submit" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update Supplier</button>
                <button type="submit" name="delete" value="1" class="px-6 py-2 ml-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete Supplier</button>
            @else
                <button type="submit" name="submit" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New Supplier Details</button>
            @endif
        </div>
    </div>

    <!-- Contacts Tab -->
    <div x-show="tab === 'contacts'" class="bg-white shadow rounded-lg p-6" x-cloak>
        <p class="text-sm text-gray-500">Contact management for this supplier will be available here.</p>
    </div>

    <!-- Transactions Tab -->
    <div x-show="tab === 'transactions'" class="bg-white shadow rounded-lg p-6" x-cloak>
        <p class="text-sm text-gray-500">Supplier transactions will be shown here.</p>
    </div>

    <!-- Orders Tab -->
    <div x-show="tab === 'orders'" class="bg-white shadow rounded-lg p-6" x-cloak>
        <p class="text-sm text-gray-500">Purchase orders for this supplier will be shown here.</p>
    </div>

    <!-- Attachments Tab -->
    <div x-show="tab === 'attachments'" class="bg-white shadow rounded-lg p-6" x-cloak>
        <p class="text-sm text-gray-500">Attachments for this supplier will be available here.</p>
    </div>
</div>

</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
