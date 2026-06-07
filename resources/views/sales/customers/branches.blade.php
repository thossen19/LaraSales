@extends('layouts.app')

@section('title', 'Customer Branches - Sales ERP')

@section('content')
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Customer Branches</h2>
            <p class="mt-2 text-gray-600">Manage customer branches with different locations, contacts, and sales settings.</p>
        </div>

        <form method="GET" action="{{ route('sales.customers.branches') }}">
            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-filter mr-2"></i>Select Customer</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <select name="customer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                                <option value="all">Select a customer...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ $selectedCustomer == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                            <i class="fas fa-search mr-1"></i>View Branches
                        </button>
                    </div>
                </div>
            </div>
            </form>

            @if($selectedCustomer !== 'all')
                @php $customer = $customers->firstWhere('id', $selectedCustomer); @endphp

                @if($branches->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-code-branch mr-2"></i>Branches for {{ $customer->name ?? '' }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Short Name</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Sales Person</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Area</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Phone</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Inactive</th>
                                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($branches as $b)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-3 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900">{{ $b->branch_ref ?? $b->branch_code }}</td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-900">{{ $b->branch_name }}</td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $b->contact_name ?? $b->contact_person ?? '-' }}</td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $b->salesPerson->name ?? '-' }}</td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $b->salesArea->area_name ?? '-' }}</td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-sm text-gray-700">{{ $b->phone }}</td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                                @if($b->inactive)
                                                    <span class="text-red-600"><i class="fas fa-check-circle"></i></span>
                                                @else
                                                    <span class="text-gray-300"><i class="fas fa-minus-circle"></i></span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('sales.customers.branches.edit', $b) }}" class="p-1.5 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded transition" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                                                    <form action="{{ route('sales.customers.branches.destroy', $b) }}" method="POST" class="inline" onsubmit="return confirm('Delete this branch?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white shadow rounded-lg p-8 text-center mb-6">
                    <i class="fas fa-code-branch text-5xl mb-3 text-gray-300"></i>
                    <p class="text-base font-medium text-gray-500">This customer does not have any branches yet.</p>
                    <p class="text-sm text-gray-400 mt-1">Create a new branch using the form below.</p>
                </div>
                @endif

                <!-- Add/Edit Branch Form -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-plus-circle mr-2"></i>Add Branch for {{ $customer->name ?? '' }}</h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('sales.customers.branches.store') }}">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $selectedCustomer }}">

                            <table class="w-full" style="border-collapse: separate; border-spacing: 0 8px;">
                                <tr class="align-top">
                                    <td class="w-1/2 pr-6" style="vertical-align: top;">
                                        <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Name and Contact</h4>
                                        <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 w-2/5 pb-0.5">Branch Name:</td>
                                                <td class="pb-0.5"><input type="text" name="branch_name" value="{{ old('branch_name') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="60" required></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Branch Short Name:</td>
                                                <td class="pb-0.5"><input type="text" name="branch_ref" value="{{ old('branch_ref') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Contact Person:</td>
                                                <td class="pb-0.5"><input type="text" name="contact_name" value="{{ old('contact_name') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="40"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Phone Number:</td>
                                                <td class="pb-0.5"><input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30" required></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Secondary Phone:</td>
                                                <td class="pb-0.5"><input type="text" name="phone2" value="{{ old('phone2') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Fax Number:</td>
                                                <td class="pb-0.5"><input type="text" name="fax" value="{{ old('fax') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">E-mail:</td>
                                                <td class="pb-0.5"><input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="55"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Document Language:</td>
                                                <td class="pb-0.5">
                                                    <select name="rep_lang" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">Customer default</option>
                                                        <option value="en_US" {{ old('rep_lang') == 'en_US' ? 'selected' : '' }}>English</option>
                                                        <option value="es_ES" {{ old('rep_lang') == 'es_ES' ? 'selected' : '' }}>Spanish</option>
                                                        <option value="fr_FR" {{ old('rep_lang') == 'fr_FR' ? 'selected' : '' }}>French</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>

                                        <h4 class="text-md font-semibold text-gray-800 mt-6 mb-4 border-b pb-2">Sales</h4>
                                        <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 w-2/5 pb-0.5">Sales Person:</td>
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
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Area:</td>
                                                <td class="pb-0.5">
                                                    <select name="area_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">None</option>
                                                        @foreach($salesAreas as $sa)
                                                            <option value="{{ $sa->id }}" {{ old('area_id') == $sa->id ? 'selected' : '' }}>{{ $sa->area_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Sales Group:</td>
                                                <td class="pb-0.5">
                                                    <select name="group_no" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">None</option>
                                                        @foreach($salesGroups as $sg)
                                                            <option value="{{ $sg->id }}" {{ old('group_no') == $sg->id ? 'selected' : '' }}>{{ $sg->group_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Default Inventory Location:</td>
                                                <td class="pb-0.5">
                                                    <select name="default_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">None</option>
                                                        @foreach($warehouses as $wh)
                                                            <option value="{{ $wh->id }}" {{ old('default_location') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Default Shipping Co:</td>
                                                <td class="pb-0.5">
                                                    <select name="default_ship_via" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">None</option>
                                                        @foreach($shippers as $sh)
                                                            <option value="{{ $sh->shipper_id }}" {{ old('default_ship_via') == $sh->shipper_id ? 'selected' : '' }}>{{ $sh->shipper_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Tax Group:</td>
                                                <td class="pb-0.5">
                                                    <select name="tax_group_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                        <option value="">None</option>
                                                        @foreach($taxGroups as $tg)
                                                            <option value="{{ $tg->id }}" {{ old('tax_group_id') == $tg->id ? 'selected' : '' }}>{{ $tg->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="w-1/2 pl-6" style="vertical-align: top;">
                                        <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Addresses</h4>
                                        <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5" style="vertical-align: top;">Mailing Address:</td>
                                                <td class="pb-0.5"><textarea name="br_post_address" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('br_post_address') }}</textarea></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5" style="vertical-align: top;">Billing Address:</td>
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
                                                <td class="text-sm font-medium text-gray-700 pb-0.5">Bank Account Number:</td>
                                                <td class="pb-0.5"><input type="text" name="bank_account" value="{{ old('bank_account') }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="60"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-sm font-medium text-gray-700 pb-0.5" style="vertical-align: top;">General Notes:</td>
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

                            <div class="mt-6 flex justify-center">
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                    <i class="fas fa-save mr-1"></i>Add New Branch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <i class="fas fa-code-branch text-6xl mb-4 text-gray-300"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">Select a Customer</h3>
                    <p class="text-gray-400">Choose a customer from the dropdown above to view and manage their branches.</p>
                </div>
            @endif
        </div>
    </div>
@endsection