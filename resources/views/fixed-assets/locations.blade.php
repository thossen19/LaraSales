@extends('layouts.app')

@section('title', 'Fixed Assets Locations')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Fixed Assets Locations</h2>
    </div>

    @if($msg)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('fixed-assets.locations') }}">
        @csrf
        <div class="bg-white shadow rounded-lg overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Location Code</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Location Name</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Address</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Phone</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Secondary Phone</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600">Inactive</th>
                        <th class="text-center px-4 py-2 font-medium text-gray-600" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $k = 0; @endphp
                    @forelse($locations as $loc)
                    <tr class="border-b hover:bg-gray-50 {{ $k % 2 ? 'bg-gray-50' : '' }}">
                        <td class="px-4 py-2">{{ $loc->loc_code }}</td>
                        <td class="px-4 py-2">{{ $loc->location_name }}</td>
                        <td class="px-4 py-2">{{ $loc->delivery_address }}</td>
                        <td class="px-4 py-2">{{ $loc->phone }}</td>
                        <td class="px-4 py-2">{{ $loc->phone2 }}</td>
                        <td class="text-center px-4 py-2">
                            <input type="checkbox" {{ $loc->inactive ? 'checked' : '' }}
                                   onclick="event.preventDefault(); document.getElementById('toggle-inactive-{{ $loc->loc_code }}').submit();">
                            <form id="toggle-inactive-{{ $loc->loc_code }}" method="POST" action="{{ route('fixed-assets.locations') }}" class="hidden">
                                @csrf
                                <input type="hidden" name="loc_code" value="{{ $loc->loc_code }}">
                            </form>
                        </td>
                        <td class="text-center px-4 py-2">
                            <button type="submit" name="Edit{{ $loc->loc_code }}" value="1"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                        </td>
                        <td class="text-center px-4 py-2">
                            <button type="submit" name="Delete{{ $loc->loc_code }}" value="1"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                    onclick="return confirm('Are you sure you want to delete this location?')">Delete</button>
                        </td>
                    </tr>
                    @php $k++; @endphp
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">No fixed asset locations defined.</td>
                    </tr>
                    @endforelse
                    <tr class="bg-gray-50 border-t">
                        <td colspan="6" class="px-4 py-2">
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2">Show Inactive</span>
                            </label>
                        </td>
                        <td colspan="2" class="px-4 py-2"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <input type="hidden" name="fixed_asset" value="1">

        <div class="bg-white shadow rounded-lg p-6">
            @if($selected_id != -1 && $selected_location)
                <input type="hidden" name="selected_id" value="{{ $selected_id }}">
                <input type="hidden" name="loc_code" value="{{ $selected_location->loc_code }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location Code:</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $selected_location->loc_code }}</p>
                    </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location Code:</label>
                        <input type="text" name="loc_code" maxlength="5" value="{{ old('loc_code') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
            @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location Name:</label>
                        <input type="text" name="location_name" maxlength="50"
                               value="{{ old('location_name', $selected_location->location_name ?? '') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Contact for deliveries:</label>
                    <input type="text" name="contact" maxlength="30"
                           value="{{ old('contact', $selected_location->contact ?? '') }}"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Address:</label>
                    <textarea name="delivery_address" rows="5" maxlength="255"
                              class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('delivery_address', $selected_location->delivery_address ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telephone No:</label>
                        <input type="text" name="phone" maxlength="30"
                               value="{{ old('phone', $selected_location->phone ?? '') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Secondary Phone Number:</label>
                        <input type="text" name="phone2" maxlength="30"
                               value="{{ old('phone2', $selected_location->phone2 ?? '') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Facsimile No:</label>
                        <input type="text" name="fax" maxlength="30"
                               value="{{ old('fax', $selected_location->fax ?? '') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">E-mail:</label>
                        <input type="email" name="email" maxlength="255"
                               value="{{ old('email', $selected_location->email ?? '') }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    @if($selected_id != -1)
                        <button type="submit" name="UPDATE_ITEM" value="1"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                        <button type="submit" name="cancel" value="1"
                                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                    @else
                        <button type="submit" name="ADD_ITEM" value="1"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New</button>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection