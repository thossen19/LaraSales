@extends('layouts.app')
@section('title', 'Dimension Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Dimension Entry</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center font-medium">{{ $message }}</div>
    <div class="mb-6 space-x-4">
        <a href="{{ route('dimensions.entry') }}" class="text-indigo-600 hover:text-indigo-900">Enter a &amp;new dimension</a>
        <a href="{{ route('dimensions.inquiries.index') }}" class="text-indigo-600 hover:text-indigo-900">&amp;Select an existing dimension</a>
        @if(request('AddedID') || request('UpdatedID'))
        <a href="{{ route('setup.attach-documents') }}?filterType=40&trans_no={{ request('AddedID') ?: request('UpdatedID') }}" class="text-indigo-600 hover:text-indigo-900">&amp;Add Attachment</a>
        @endif
    </div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('dimensions.entry') }}">
@csrf

<div class="bg-white shadow rounded-lg p-6 max-w-lg">
    <div class="space-y-4">
        @if($selected_id)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension Reference:</label>
                <div class="text-sm text-gray-900 font-medium py-2">{{ $ref }}</div>
                <input type="hidden" name="ref" value="{{ $ref }}">
            </div>
            <input type="hidden" name="selected_id" value="{{ $selected_id }}">
        @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dimension Reference:</label>
                <input type="text" name="ref" value="{{ $ref }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name:</label>
            <input type="text" name="name" value="{{ $name }}" maxlength="75" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type:</label>
            <select name="type_" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @for($i = 1; $i <= ($use_dimension ?: 1); $i++)
                    <option value="{{ $i }}" {{ $type_ == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date:</label>
            <input type="date" name="date_" value="{{ $date_ }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date Required By:</label>
            <input type="date" name="due_date" value="{{ $due_date }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tags:</label>
            <select name="dimension_tags[]" multiple size="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach($dim_tags as $tag)
                    <option value="{{ $tag->id }}" {{ in_array($tag->id, (array)$dimension_tags) ? 'selected' : '' }}>{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Memo:</label>
            <textarea name="memo_" rows="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $memo_ }}</textarea>
        </div>
    </div>
</div>

@if($closed)
    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-800">This Dimension is closed.</div>
@endif

<div class="mt-6 flex items-center space-x-4">
    @if($selected_id)
        <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
        @if($closed)
            <button type="submit" name="reopen" value="1" class="px-6 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition">Re-open This Dimension</button>
        @else
            <button type="submit" name="close" value="1" class="px-6 py-2 bg-yellow-600 text-white font-medium rounded-md hover:bg-yellow-700 transition">Close This Dimension</button>
        @endif
        <button type="submit" name="delete" value="1" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition" onclick="return confirm('Are you sure?')">Delete This Dimension</button>
    @else
        <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add</button>
    @endif
</div>

</form>
@endsection