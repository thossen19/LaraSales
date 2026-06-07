@extends('layouts.app')
@section('title', 'GL Account Groups - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">GL Account Groups</h2>
    <p class="mt-2 text-gray-600">Manage general ledger account groups.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.gl-groups') }}">
@csrf
<input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subgroup Of</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($groups as $g)
                @php
                    $parent_name = $g->parent == '-1' ? '' : \DB::table('chart_types')->where('id', $g->parent)->value('name');
                    $class_name = \DB::table('chart_class')->where('cid', $g->class_id)->value('class_name');
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $g->id }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900"><a href="{{ route('banking.gl-accounts') }}?id={{ $g->id }}" class="text-indigo-600 hover:text-indigo-900">{{ $g->name }}</a></td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $parent_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $class_name }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('banking.gl-groups', ['toggle_inactive' => $g->id, 'show_inactive' => $show_inactive ? '1' : null, 'cid' => $filter_cid ?: null]) }}" class="text-sm {{ $g->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">{{ $g->inactive ? 'Yes' : 'No' }}</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="this.form.selected_id.value='{{ $g->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="this.form.selected_id.value='{{ $g->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No account groups defined.</td>
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
        {{ $selected_id ? 'Edit Account Group' : 'Add New Account Group' }}
    </h3>

    @if($selected_id)
        <input type="hidden" name="old_id" value="{{ $old_id }}">
    @endif

    <div class="space-y-4 max-w-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ID:</label>
            <input type="text" name="id" value="{{ old('id', $edit_id_value) }}" maxlength="10" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name:</label>
            <input type="text" name="name" value="{{ old('name', $edit_name) }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subgroup Of:</label>
            <select name="parent" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">-- None --</option>
                @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ $edit_parent == $g->id ? 'selected' : '' }}>{{ $g->id }} {{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class:</label>
            <select name="class_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @foreach($classes as $c)
                    <option value="{{ $c->cid }}" {{ $edit_class_id == $c->cid ? 'selected' : '' }}>{{ $c->class_name }}</option>
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