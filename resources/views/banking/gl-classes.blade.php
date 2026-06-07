@extends('layouts.app')
@section('title', 'GL Account Classes - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">GL Account Classes</h2>
    <p class="mt-2 text-gray-600">Manage general ledger account classes.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.gl-classes') }}">
@csrf
<input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Type</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($classes as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $c->cid }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900"><a href="{{ route('banking.gl-groups', ['cid' => $c->cid]) }}" class="text-indigo-600 hover:text-indigo-900">{{ $c->class_name }}</a></td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $class_types[$c->ctype] ?? $c->ctype }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('banking.gl-classes', ['toggle_inactive' => $c->cid, 'show_inactive' => $show_inactive ? '1' : null]) }}" class="text-sm {{ $c->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $c->inactive ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $c->cid }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $c->cid }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No account classes defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mb-4">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id ? 'Edit Account Class' : 'Add New Account Class' }}
    </h3>

    <div class="space-y-4 max-w-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class ID:</label>
            @if($selected_id)
                <div class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700">{{ $edit_id_value }}</div>
                <input type="hidden" name="id" value="{{ $edit_id_value }}">
            @else
                <input type="text" name="id" value="{{ old('id', $edit_id_value) }}" maxlength="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class Name:</label>
            <input type="text" name="name" value="{{ old('name', $edit_name) }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class Type:</label>
            <select name="ctype" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @foreach($class_types as $val => $label)
                    <option value="{{ $val }}" {{ $edit_ctype == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
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