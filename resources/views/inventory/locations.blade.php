@extends('layouts.app')
@section('title', 'Inventory Locations - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Inventory Locations</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('inventory.locations') }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secondary Phone</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($locations as $loc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $loc->loc_code }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $loc->location_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $loc->delivery_address }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $loc->phone }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $loc->phone2 }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('inventory.locations', ['toggle_inactive' => $loc->loc_code, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $loc->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $loc->inactive ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $loc->loc_code }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $loc->loc_code }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No locations defined.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray-50">
                <td colspan="6" class="px-4 py-3">
                    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Show also inactive</span>
                    </label>
                </td>
                <td class="px-4 py-3"></td>
                <td class="px-4 py-3"></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-lg">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id ? 'Edit Location' : 'Add New Location' }}
    </h3>

    <input type="hidden" name="selected_id" value="{{ $selected_id }}">

    <div class="space-y-4">
        @if($selected_id)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location Code:</label>
                <div class="text-sm text-gray-900 font-medium py-2">{{ $loc_code }}</div>
            </div>
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location Code:</label>
                <input type="text" name="loc_code" value="{{ $loc_code }}" maxlength="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Location Name:</label>
            <input type="text" name="location_name" value="{{ $location_name }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact for deliveries:</label>
            <input type="text" name="contact" value="{{ $contact }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
            <textarea name="delivery_address" rows="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $delivery_address }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telephone No:</label>
            <input type="text" name="phone" value="{{ $phone }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone Number:</label>
            <input type="text" name="phone2" value="{{ $phone2 }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Facsimile No:</label>
            <input type="text" name="fax" value="{{ $fax }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail:</label>
            <input type="email" name="email" value="{{ $email }}" maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div class="pt-4 mt-4 border-t border-gray-200">
        @if($selected_id)
            <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
            <button type="submit" name="Mode" value="RESET" class="px-6 py-2 ml-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
        @else
            <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
        @endif
    </div>
</div>

</form>
@endsection