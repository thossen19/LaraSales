@extends('layouts.app')

@section('title', 'Work Centres - Manufacturing')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Work Centres</h2>
    </div>

    @if($msg)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
    @endif
    @if($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('manufacturing.work-centers') }}">
        @csrf

        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3">Name</th>
                        <th class="text-left px-4 py-3">Description</th>
                        <th class="text-center px-4 py-3">Inactive</th>
                        <th class="text-center px-4 py-3" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($work_centres as $wc)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $wc->name }}</td>
                        <td class="px-4 py-3">{{ $wc->description }}</td>
                        <td class="text-center px-4 py-3">
                            @if($wc->inactive)
                                <span class="text-red-600"><i class="fas fa-times-circle"></i></span>
                            @else
                                <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                            @endif
                        </td>
                        <td class="text-center px-4 py-3">
                            <button type="submit" name="Edit{{ $wc->id }}" value="1"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                        </td>
                        <td class="text-center px-4 py-3">
                            <button type="submit" name="Delete{{ $wc->id }}" value="1"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                    onclick="return confirm('Are you sure you want to delete this work centre?')">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            @if($selected_id > 0)
                <input type="hidden" name="selected_id" value="{{ $selected_id }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" name="name" value="{{ old('name', $selected_centre->name ?? '') }}" maxlength="40" required
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description:</label>
                    <input type="text" name="description" value="{{ old('description', $selected_centre->description ?? '') }}" maxlength="50"
                           class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                @if($selected_id > 0)
                    <button type="submit" name="UPDATE_ITEM" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                    <button type="submit" name="delete_work_centre" value="1"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                            onclick="return confirm('Are you sure you want to delete this work centre?')">Delete</button>
                    <button type="submit" name="cancel" value="1"
                            class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                @else
                    <button type="submit" name="ADD_ITEM" value="1"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New</button>
                @endif
            </div>
        </div>
    </form>
@endsection