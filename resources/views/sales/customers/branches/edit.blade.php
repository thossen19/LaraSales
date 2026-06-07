@extends('layouts.app')

@section('title', 'Edit Branch - Sales ERP')

@section('content')
    <div class="flex gap-6">
        @include('components.sales-sidebar')
        
        <div class="flex-1">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Branch: {{ $branch->branch_name }}</h2>
                    <p class="mt-1 text-gray-600">Customer: {{ $branch->customer->name ?? 'N/A' }} | Code: {{ $branch->branch_code }}</p>
                </div>
                <a href="{{ route('sales.customers.branches', ['customer_id' => $branch->customer_id]) }}" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-100 transition border border-gray-300">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                    <h3 class="text-lg font-semibold text-white"><i class="fas fa-code-branch mr-2"></i>Branch Settings</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('sales.customers.branches.update', $branch) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="customer_id" value="{{ $branch->customer_id }}">

                        <table class="w-full" style="border-collapse: separate; border-spacing: 0 8px;">
                            <tr class="align-top">
                                <td class="w-1/2 pr-6" style="vertical-align: top;">
                                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Name and Contact</h4>
                                    <table class="w-full" style="border-collapse: separate; border-spacing: 0 6px;">
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 w-2/5 pb-0.5">Branch Name:</td>
                                            <td class="pb-0.5"><input type="text" name="branch_name" value="{{ old('branch_name', $branch->branch_name) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="60" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Branch Short Name:</td>
                                            <td class="pb-0.5"><input type="text" name="branch_ref" value="{{ old('branch_ref', $branch->branch_ref) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Contact Person:</td>
                                            <td class="pb-0.5"><input type="text" name="contact_name" value="{{ old('contact_name', $branch->contact_name) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="40"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Phone Number:</td>
                                            <td class="pb-0.5"><input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Secondary Phone:</td>
                                            <td class="pb-0.5"><input type="text" name="phone2" value="{{ old('phone2', $branch->phone2) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Fax Number:</td>
                                            <td class="pb-0.5"><input type="text" name="fax" value="{{ old('fax', $branch->fax) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="30"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">E-mail:</td>
                                            <td class="pb-0.5"><input type="email" name="email" value="{{ old('email', $branch->email) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="55"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Document Language:</td>
                                            <td class="pb-0.5">
                                                <select name="rep_lang" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="">Customer default</option>
                                                    <option value="en_US" {{ old('rep_lang', $branch->rep_lang) == 'en_US' ? 'selected' : '' }}>English</option>
                                                    <option value="es_ES" {{ old('rep_lang', $branch->rep_lang) == 'es_ES' ? 'selected' : '' }}>Spanish</option>
                                                    <option value="fr_FR" {{ old('rep_lang', $branch->rep_lang) == 'fr_FR' ? 'selected' : '' }}>French</option>
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
                                                        <option value="{{ $sp->id }}" {{ old('sales_person_id', $branch->sales_person_id) == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
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
                                                        <option value="{{ $sa->id }}" {{ old('area_id', $branch->area_id) == $sa->id ? 'selected' : '' }}>{{ $sa->area_name }}</option>
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
                                                        <option value="{{ $sg->id }}" {{ old('group_no', $branch->group_no) == $sg->id ? 'selected' : '' }}>{{ $sg->group_name }}</option>
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
                                                        <option value="{{ $wh->id }}" {{ old('default_location', $branch->default_location) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
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
                                                        <option value="{{ $sh->shipper_id }}" {{ old('default_ship_via', $branch->default_ship_via) == $sh->shipper_id ? 'selected' : '' }}>{{ $sh->shipper_name }}</option>
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
                                                        <option value="{{ $tg->id }}" {{ old('tax_group_id', $branch->tax_group_id) == $tg->id ? 'selected' : '' }}>{{ $tg->name }}</option>
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
                                            <td class="text-sm font-medium text-gray-700 pb-0.5 w-2/5" style="vertical-align: top;">Mailing Address:</td>
                                            <td class="pb-0.5"><textarea name="br_post_address" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('br_post_address', $branch->br_post_address) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5" style="vertical-align: top;">Billing Address:</td>
                                            <td class="pb-0.5"><textarea name="address" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>{{ old('address', $branch->address) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">City:</td>
                                            <td class="pb-0.5"><input type="text" name="city" value="{{ old('city', $branch->city) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">State:</td>
                                            <td class="pb-0.5"><input type="text" name="state" value="{{ old('state', $branch->state) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Postal Code:</td>
                                            <td class="pb-0.5"><input type="text" name="postal_code" value="{{ old('postal_code', $branch->postal_code) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="20" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Country:</td>
                                            <td class="pb-0.5"><input type="text" name="country" value="{{ old('country', $branch->country) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="100" required></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Bank Account Number:</td>
                                            <td class="pb-0.5"><input type="text" name="bank_account" value="{{ old('bank_account', $branch->bank_account) }}" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" maxlength="60"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5">Inactive:</td>
                                            <td class="pb-0.5">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="inactive" value="1" {{ old('inactive', $branch->inactive) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                    <span class="ml-2 text-sm text-gray-600">Branch is inactive</span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm font-medium text-gray-700 pb-0.5" style="vertical-align: top;">General Notes:</td>
                                            <td class="pb-0.5"><textarea name="notes" rows="3" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $branch->notes) }}</textarea></td>
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
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition shadow-sm">
                                <i class="fas fa-save mr-1"></i>Update Branch
                            </button>
                            <button type="button" onclick="if(confirm('Delete this branch?')) { document.getElementById('delete-form').submit(); }" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    </form>
                    <form id="delete-form" action="{{ route('sales.customers.branches.destroy', $branch) }}" method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection