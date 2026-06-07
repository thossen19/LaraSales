@extends('layouts.app')
@section('title', 'Printer Locations - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Printer Locations</h2>
    <p class="mt-2 text-gray-600">Manage printers for report output.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Host</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Printer Queue</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($printers as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $p->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->description }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->host }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->queue }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.printers', ['Mode' => 'Edit', 'selected_id' => $p->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.printers', ['Mode' => 'Delete', 'selected_id' => $p->id]) }}" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No printers defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_printer ? 'Edit Printer' : 'Add New Printer' }}
    </h3>

    <form method="POST" action="{{ route('setup.printers', $edit_printer ? ['Mode' => 'UPDATE_ITEM', 'selected_id' => $edit_printer->id] : ['Mode' => 'ADD_ITEM']) }}">
        @csrf

        <div class="space-y-4 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Printer Name:</label>
                <input type="text" name="name" value="{{ old('name', $edit_printer->name ?? '') }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Printer Description:</label>
                <input type="text" name="descr" value="{{ old('descr', $edit_printer->description ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Host name or IP:</label>
                <input type="text" name="host" value="{{ old('host', $edit_printer->host ?? 'localhost') }}" maxlength="40" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port:</label>
                <input type="text" name="port" value="{{ old('port', $edit_printer->port ?? '515') }}" maxlength="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Printer Queue:</label>
                <input type="text" name="queue" value="{{ old('queue', $edit_printer->queue ?? '') }}" maxlength="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Timeout:</label>
                <input type="text" name="tout" value="{{ old('tout', $edit_printer->timeout ?? '0') }}" maxlength="5" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_printer ? 'Update' : 'Add New' }}
            </button>
            @if($edit_printer)
                <a href="{{ route('setup.printers', ['Mode' => 'Cancel']) }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection