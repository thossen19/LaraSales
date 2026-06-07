@extends('layouts.app')
@section('title', 'Printing Profiles - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Printing Profiles</h2>
    <p class="mt-2 text-gray-600">Configure printing profiles for report output destinations.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('setup.print-profiles') }}" class="mb-4">
    <div class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700">Select printing profile:</label>
        <select name="profile_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">New printing profile</option>
            @foreach($profiles as $p)
                <option value="{{ $p }}" {{ $profile_id == $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
        <noscript><button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded">Select</button></noscript>
    </div>
</form>

<hr class="mb-6">

<form method="POST" action="{{ route('setup.print-profiles') }}">
    @csrf

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-4">
            @if($profile_id)
                <div class="flex items-center">
                    <label class="block text-sm font-medium text-gray-700 w-48">Printing Profile Name:</label>
                    <span class="text-sm text-gray-900 py-2">{{ $profile_id }}</span>
                </div>
                <input type="hidden" name="profile_id" value="{{ $profile_id }}">
            @else
                <div class="flex items-center">
                    <label class="block text-sm font-medium text-gray-700 w-48">Printing Profile Name:</label>
                    <input type="text" name="name" maxlength="30" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Report Id</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-64">Printer</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reports as $rep => $descr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-700 text-center font-mono">{{ $rep === '' ? '-' : $rep }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $descr }}</td>
                            <td class="px-4 py-2">
                                <select name="Prn{{ $rep }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">{{ $rep === '' ? 'Browser support' : 'Default' }}</option>
                                    @foreach($printers as $pval => $plabel)
                                        @if($pval !== '')
                                            <option value="{{ $pval }}" {{ (isset($prints[$rep]) && $prints[$rep] == $pval) ? 'selected' : '' }}>{{ $plabel }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 px-6 py-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-3">
        @if($profile_id)
            <button type="submit" name="submit" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Update Profile
            </button>
            <button type="submit" name="delete" value="1" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150" onclick="return confirm('Delete this printing profile?');">
                Delete Profile
            </button>
        @else
            <button type="submit" name="submit" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                Add New Profile
            </button>
        @endif
    </div>
</form>
@endsection