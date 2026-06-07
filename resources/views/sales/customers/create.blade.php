@extends('layouts.app')

@section('title', 'Add Customer - Sales ERP')

@section('content')
    <div class="flex gap-6">
        @include('components.sales-sidebar')
        
        <div class="flex-1">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Add New Customer</h2>
                <p class="mt-2 text-gray-600">Create a new customer account.</p>
            </div>

            <form method="POST" action="{{ route('sales.customers.store') }}">
            @csrf
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-building mr-2"></i>General Settings</h3>
                </div>
                <div class="p-6">
                    <table class="w-full" style="border-collapse: separate; border-spacing: 0 8px;">
                        <tr class="align-top">
                            <td class="w-1/2 pr-6" style="vertical-align: top;">
                                <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Name and Address</h4>
                                <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 w-1/3 pb-0.5">Customer Name:</td>
                                        <td class="pb-0.5"><input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="80" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Customer Short Name:</td>
                                        <td class="pb-0.5"><input type="text" name="cust_ref" value="{{ old('cust_ref') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Address:</td>
                                        <td class="pb-0.5"><textarea name="address" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>{{ old('address') }}</textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">City:</td>
                                        <td class="pb-0.5"><input type="text" name="city" value="{{ old('city') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">State:</td>
                                        <td class="pb-0.5"><input type="text" name="state" value="{{ old('state') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Postal Code:</td>
                                        <td class="pb-0.5"><input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="20" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Country:</td>
                                        <td class="pb-0.5"><input type="text" name="country" value="{{ old('country') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">GSTNo:</td>
                                        <td class="pb-0.5"><input type="text" name="tax_id" value="{{ old('tax_id') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Phone:</td>
                                        <td class="pb-0.5"><input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Fax:</td>
                                        <td class="pb-0.5"><input type="text" name="fax" value="{{ old('fax') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="50"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Email:</td>
                                        <td class="pb-0.5"><input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="55"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Customer's Currency:</td>
                                        <td class="pb-0.5">
                                            <select name="curr_code" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                @foreach($currencies as $code => $label)
                                                    <option value="{{ $code }}" {{ old('curr_code', 'USD') == $code ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Type/Price List:</td>
                                        <td class="pb-0.5">
                                            <select name="sales_type_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Default</option>
                                                @foreach($salesTypes as $st)
                                                    <option value="{{ $st->id }}" {{ old('sales_type_id') == $st->id ? 'selected' : '' }}>{{ $st->type_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="w-1/2 pl-6" style="vertical-align: top;">
                                <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Sales</h4>
                                <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 w-2/5 pb-0.5">Discount Percent:</td>
                                        <td class="pb-0.5"><input type="number" name="discount" value="{{ old('discount', 0) }}" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Prompt Payment Discount Percent:</td>
                                        <td class="pb-0.5"><input type="number" name="pymt_discount" value="{{ old('pymt_discount', 0) }}" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Credit Limit:</td>
                                        <td class="pb-0.5"><input type="number" name="credit_limit" value="{{ old('credit_limit', 0) }}" step="0.01" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Payment Terms:</td>
                                        <td class="pb-0.5">
                                            <select name="payment_terms" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="0">Cash Sale</option>
                                                <option value="7" {{ old('payment_terms') == 7 ? 'selected' : '' }}>7 Days</option>
                                                <option value="10" {{ old('payment_terms') == 10 ? 'selected' : '' }}>10 Days</option>
                                                <option value="14" {{ old('payment_terms') == 14 ? 'selected' : '' }}>14 Days</option>
                                                <option value="15" {{ old('payment_terms') == 15 ? 'selected' : '' }}>15 Days</option>
                                                <option value="21" {{ old('payment_terms') == 21 ? 'selected' : '' }}>21 Days</option>
                                                <option value="30" {{ old('payment_terms', 30) == 30 ? 'selected' : '' }}>30 Days</option>
                                                <option value="45" {{ old('payment_terms') == 45 ? 'selected' : '' }}>45 Days</option>
                                                <option value="60" {{ old('payment_terms') == 60 ? 'selected' : '' }}>60 Days</option>
                                                <option value="90" {{ old('payment_terms') == 90 ? 'selected' : '' }}>90 Days</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Credit Status:</td>
                                        <td class="pb-0.5">
                                            <select name="credit_status_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">None</option>
                                                @foreach($creditStatuses as $cs)
                                                    <option value="{{ $cs->id }}" {{ old('credit_status_id') == $cs->id ? 'selected' : '' }}>{{ $cs->status_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Group:</td>
                                        <td class="pb-0.5">
                                            <select name="sales_group_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">None</option>
                                                @foreach($salesGroups as $sg)
                                                    <option value="{{ $sg->id }}" {{ old('sales_group_id') == $sg->id ? 'selected' : '' }}>{{ $sg->group_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Person:</td>
                                        <td class="pb-0.5">
                                            <select name="sales_person_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">None</option>
                                                @foreach($salesPersons as $sp)
                                                    <option value="{{ $sp->id }}" {{ old('sales_person_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Contact Person:</td>
                                        <td class="pb-0.5"><input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="255" required></td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">Status:</td>
                                        <td class="pb-0.5">
                                            <select name="status" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="hold" {{ old('status') == 'hold' ? 'selected' : '' }}>Hold</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-sm font-medium text-gray-700 pb-0.5">General Notes:</td>
                                        <td class="pb-0.5"><textarea name="notes" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    @if ($errors->any())
                        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-center gap-3">
                        <a href="{{ route('sales.customers.index') }}" class="px-5 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-1"></i>Add New Customer
                        </button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection