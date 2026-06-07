@extends('layouts.app')
@section('title', 'Create/Update Company - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Create/Update Company</h2>
    <p class="mt-2 text-gray-600">Create and manage companies.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<div class="bg-white shadow rounded-lg overflow-hidden mb-4">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Default</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @php $current = session('current_company_id', auth()->user()->company_id ?? 1); @endphp
            @forelse($companies as $c)
                <tr class="hover:bg-gray-50 {{ $c->id == $current ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium whitespace-nowrap">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->email }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->phone }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->city }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $c->country }}</td>
                    <td class="px-4 py-3 text-center text-sm {{ $c->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $c->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-center text-sm {{ $def_coy == $c->id ? 'text-green-600 font-medium' : 'text-gray-400' }}">{{ $def_coy == $c->id ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.companies', ['Mode' => 'Edit', 'selected_id' => $c->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($c->id == $current)
                            <span class="text-gray-400 text-sm">&nbsp;</span>
                        @else
                            <a href="{{ route('setup.companies', ['Mode' => 'Delete', 'selected_id' => $c->id]) }}" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('You are about to remove company \'{{ $c->name }}\'.\nDo you want to continue?')">Delete</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No companies defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-4">
    The marked company is the current company which cannot be deleted.
</div>

<hr class="mb-6">

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_coy ? 'Edit Company' : 'Add New Company' }}
    </h3>

    <form method="POST" action="{{ route('setup.companies', $edit_coy ? ['Mode' => 'UPDATE_ITEM', 'selected_id' => $edit_coy->id] : ['Mode' => 'ADD_ITEM']) }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company:</label>
                    <input type="text" name="name" value="{{ old('name', $edit_coy->name ?? '') }}" maxlength="255" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                    <input type="email" name="email" value="{{ old('email', $edit_coy->email ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone:</label>
                    <input type="text" name="phone" value="{{ old('phone', $edit_coy->phone ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
                    <input type="text" name="address" value="{{ old('address', $edit_coy->address ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City:</label>
                    <input type="text" name="city" value="{{ old('city', $edit_coy->city ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country:</label>
                    <input type="text" name="country" value="{{ old('country', $edit_coy->country ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code:</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $edit_coy->postal_code ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax ID:</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $edit_coy->tax_id ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number:</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number', $edit_coy->registration_number ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website:</label>
                    <input type="text" name="website" value="{{ old('website', $edit_coy->website ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo:</label>
            @if($edit_coy && $edit_coy->logo)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $edit_coy->logo) }}" alt="Logo" class="h-16 w-auto">
                </div>
            @endif
            <input type="file" name="logo" class="text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes:</label>
            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $edit_coy->notes ?? '') }}</textarea>
        </div>

        <div class="mt-4 flex items-center gap-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $edit_coy->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="default" value="1" {{ $def_coy == ($edit_coy->id ?? 0) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Default Company</span>
            </label>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">
                {{ $edit_coy ? 'Update' : 'Add New' }}
            </button>
            @if($edit_coy)
                <a href="{{ route('setup.companies') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection