@extends('layouts.app')
@section('title', 'Backup and Restore Database - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Backup and Restore Database</h2>
    <p class="mt-2 text-gray-600">Create and manage database backups.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $message !!}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('setup.backup') }}" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Create backup</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments:</label>
                    <textarea name="comments" rows="8" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ old('comments') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Compression:</label>
                    <select name="comp" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="no">No</option>
                        @if($hasZip)
                            <option value="zip">zip</option>
                        @endif
                        @if($hasGzip)
                            <option value="gzip">gzip</option>
                        @endif
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit" name="creat" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Create Backup</button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Backup scripts maintenance</h3>

            <div class="mb-4">
                <select name="backups" size="2" style="height:160px;min-width:230px" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @forelse($files as $f)
                        <option value="{{ $f }}" {{ request('backups') == $f ? 'selected' : '' }}>{{ $f }}</option>
                    @empty
                        <option value="" disabled>No backup files found</option>
                    @endforelse
                </select>
            </div>

            <div class="flex flex-wrap gap-2 mb-4">
                <button type="submit" name="view" value="1" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">View Backup</button>
                <button type="submit" name="download" value="1" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">Download Backup</button>
                <button type="submit" name="restore" value="1" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600 transition" onclick="return confirm('You are about to restore database from backup file.\nDo you want to continue?')">Restore Backup</button>
                <button type="submit" name="deldump" value="1" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition" onclick="return confirm('You are about to remove selected backup file.\nDo you want to continue?')">Delete Backup</button>
            </div>

            <div class="mb-4">
                <label class="flex items-center mb-2">
                    <input type="radio" name="protect" value="0" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Update security settings</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="protect" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Protect security settings</span>
                </label>
            </div>

            <div class="flex items-center gap-3">
                <input type="file" name="uploadfile" class="text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <button type="submit" name="upload" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Upload file</button>
            </div>
        </div>
    </div>
</form>
@endsection