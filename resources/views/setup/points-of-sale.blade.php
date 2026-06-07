@extends('layouts.app')
@section('title', 'POS Settings - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">POS Settings</h2>
    <p class="mt-2 text-gray-600">Manage points of sale.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.points-of-sale') }}" class="mb-4">
    @csrf
    <input type="hidden" name="action" value="toggle_show_inactive">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">POS Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credit sale</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cash sale</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Default account</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($points as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $p->pos_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $p->credit_sale ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $p->cash_sale ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->pos_location }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->pos_account }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.points-of-sale') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $p->id }}">
                            <button type="submit" class="text-sm {{ $p->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $p->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.points-of-sale') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $p->id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.points-of-sale') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this point of sale?');">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="{{ $p->id }}">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No points of sale defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_pt ? 'Edit Point of Sale' : 'Add New Point of Sale' }}
    </h3>

    @if($accounts->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-yellow-700">To have cash POS first define at least one cash bank account.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('setup.points-of-sale') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_pt ? 'update' : 'add' }}">
        @if($edit_pt)
            <input type="hidden" name="selected_id" value="{{ $edit_pt->id }}">
        @endif

        <div class="space-y-4 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Point of Sale Name:</label>
                <input type="text" name="name" value="{{ old('name', $edit_pt->pos_name ?? '') }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @if(!$accounts->isEmpty())
                <label class="flex items-center">
                    <input type="checkbox" name="credit" value="1" {{ old('credit', $edit_pt->credit_sale ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Allowed credit sale terms selection:</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="cash" value="1" {{ old('cash', $edit_pt->cash_sale ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Allowed cash sale terms selection:</span>
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default cash account:</label>
                    <select name="account" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- None --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->code }}" {{ old('account', $edit_pt->pos_account ?? '') == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="credit" value="1">
                <input type="hidden" name="cash" value="0">
                <input type="hidden" name="account" value="0">
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">POS location:</label>
                <select name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- None --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('location', $edit_pt->pos_location ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_pt ? 'Update' : 'Add New' }}
            </button>
            @if($edit_pt)
                <a href="{{ route('setup.points-of-sale') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection