@extends('layouts.app')
@section('title', 'Fixed Asset Classes')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Fixed Asset Classes</h2>
    <p class="mt-2 text-gray-600">Define fixed asset classes for depreciation and classification.</p>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('fixed-assets.classes') }}" class="bg-white shadow rounded-lg mb-6">
    @csrf

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fixed asset class</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic Depreciation Rate</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($classes as $class)
            <tr class="hover:bg-gray-50 {{ $class->inactive ? 'text-gray-400' : '' }}">
                <td class="px-4 py-2 text-sm">{{ $class->fa_class_id }}</td>
                <td class="px-4 py-2 text-sm">{{ $class->description }}</td>
                <td class="px-4 py-2 text-sm">{{ $class->depreciation_rate }}%</td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="toggle_inactive" value="{{ $class->fa_class_id }}"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $class->inactive ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $class->inactive ? 'Yes' : 'No' }}
                    </button>
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <button type="submit" name="Edit{{ $class->fa_class_id }}" value="1"
                        class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                    <button type="submit" name="Delete{{ $class->fa_class_id }}" value="1"
                        class="text-red-600 hover:text-red-900"
                        onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
        @if(!$show_inactive)
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="5" class="px-4 py-2 text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="show_inactive" value="1" onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Show also inactive</span>
                    </label>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
</form>

<form method="POST" action="{{ route('fixed-assets.classes') }}" class="bg-white shadow rounded-lg">
    @csrf
    @if($selected_id != -1)
        <input type="hidden" name="selected_id" value="{{ $selected_id }}">
    @endif

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            @if($selected_id != -1 && $selected_class)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent class:</label>
                    <p class="text-sm text-gray-900 py-2 px-3 bg-gray-50 rounded-md">{{ $selected_class->parent_id ?? 'None' }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fixed asset class:</label>
                    <p class="text-sm text-gray-900 py-2 px-3 bg-gray-50 rounded-md">{{ $selected_class->fa_class_id }}</p>
                </div>
                <input type="hidden" name="fa_class_id" value="{{ $selected_class->fa_class_id }}">
                <input type="hidden" name="parent_id" value="{{ $selected_class->parent_id }}">
            @else
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent class:</label>
                    <select name="parent_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">None</option>
                        @foreach($all_classes as $ac)
                            <option value="{{ $ac->fa_class_id }}" {{ old('parent_id') == $ac->fa_class_id ? 'selected' : '' }}>{{ $ac->fa_class_id }} - {{ $ac->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fixed asset class:</label>
                    <input type="text" name="fa_class_id" value="{{ old('fa_class_id') }}" maxlength="11"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                <input type="text" name="description" value="{{ old('description', $selected_class->description ?? '') }}" maxlength="200"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Long description:</label>
                <textarea name="long_description" rows="3"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('long_description', $selected_class->long_description ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Basic Depreciation Rate:</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="depreciation_rate" step="any"
                        value="{{ old('depreciation_rate', $selected_class->depreciation_rate ?? '') }}"
                        class="w-32 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm text-gray-500">%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200">
        @if($selected_id != -1 && $selected_class)
            <button type="submit" name="UPDATE_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update
            </button>
            <a href="{{ route('fixed-assets.classes') }}"
                class="ml-2 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">
                Cancel
            </a>
        @else
            <button type="submit" name="ADD_ITEM"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Add new
            </button>
        @endif
    </div>
</form>
@endsection