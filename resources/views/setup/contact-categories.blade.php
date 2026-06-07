@extends('layouts.app')
@section('title', 'Contact Categories - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Contact Categories</h2>
    <p class="mt-2 text-gray-600">Manage CRM contact categories.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('setup.contact-categories') }}" class="mb-4">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category Subtype</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Short Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $cat->type }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $cat->action }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $cat->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.contact-categories', ['toggle_inactive' => $cat->id, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $cat->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                            {{ $cat->inactive ? 'Yes' : 'No' }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.contact-categories', ['Mode' => 'Edit', 'selected_id' => $cat->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($cat->system)
                            <span class="text-gray-400 text-sm">&nbsp;</span>
                        @else
                            <a href="{{ route('setup.contact-categories', ['Mode' => 'Delete', 'selected_id' => $cat->id]) }}" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No contact categories defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_cat ? 'Edit Contact Category' : 'Add New Contact Category' }}
    </h3>

    <form method="POST" action="{{ route('setup.contact-categories', $edit_cat ? ['Mode' => 'UPDATE_ITEM', 'selected_id' => $edit_cat->id] : ['Mode' => 'ADD_ITEM']) }}">
        @csrf

        <div class="space-y-4 max-w-lg">
            @if($edit_cat && $edit_cat->system)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Category Type:</label>
                    <p class="py-2 text-sm text-gray-800">{{ $edit_cat->type }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Category Subtype:</label>
                    <p class="py-2 text-sm text-gray-800">{{ $edit_cat->action }}</p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Category Type:</label>
                    <input type="text" name="type" value="{{ old('type', $edit_cat->type ?? '') }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Category Subtype:</label>
                    <input type="text" name="subtype" value="{{ old('subtype', $edit_cat->action ?? '') }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category Short Name:</label>
                <input type="text" name="name" value="{{ old('name', $edit_cat->name ?? '') }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category Description:</label>
                <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $edit_cat->description ?? '') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_cat ? 'Update' : 'Add New' }}
            </button>
            @if($edit_cat)
                <a href="{{ route('setup.contact-categories', ['Mode' => 'Cancel']) }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection