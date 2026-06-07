@extends('layouts.app')
@section('title', 'Fiscal Years - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Fiscal Years</h2>
    <p class="mt-2 text-gray-600">Manage fiscal year periods, open and close fiscal years.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <p class="text-sm text-yellow-800">
        <i class="fas fa-exclamation-triangle text-yellow-600 mr-1"></i>
        Warning: Deleting a fiscal year all transactions are removed and converted into relevant balances. This process is irreversible!
    </p>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiscal Year Begin</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiscal Year End</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Closed</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($years as $fy)
                <tr class="hover:bg-gray-50 {{ $fy->id == $current_fy_id ? 'bg-indigo-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $fy->begin->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $fy->end->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $fy->closed ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.fiscal-years') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $fy->id }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($fy->id != $current_fy_id)
                            <form method="POST" action="{{ route('setup.fiscal-years') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete fiscal year {{ $fy->begin->format('d/m/Y') }} - {{ $fy->end->format('d/m/Y') }}? All transactions are deleted and converted into relevant balances. Do you want to continue?');">
                                @csrf
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="selected_id" value="{{ $fy->id }}">
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No fiscal years defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(count($years) > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-700">
            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
            The marked fiscal year is the current fiscal year which cannot be deleted.
        </p>
    </div>
@endif

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_fy ? 'Edit Fiscal Year' : 'Add New Fiscal Year' }}
    </h3>
    <form method="POST" action="{{ route('setup.fiscal-years') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_fy ? 'update' : 'add' }}">
        @if($edit_fy)
            <input type="hidden" name="selected_id" value="{{ $edit_fy->id }}">
        @endif

        @if($edit_fy)
            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year Begin:</label>
                    <p class="text-sm text-gray-900 py-2 px-3 bg-gray-50 rounded-md">{{ $edit_fy->begin->format('d/m/Y') }}</p>
                    <input type="hidden" name="from_date" value="{{ $edit_fy->begin->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year End:</label>
                    <p class="text-sm text-gray-900 py-2 px-3 bg-gray-50 rounded-md">{{ $edit_fy->end->format('d/m/Y') }}</p>
                    <input type="hidden" name="to_date" value="{{ $edit_fy->end->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Is Closed:</label>
                    <select name="closed" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="0" {{ !$edit_fy->closed ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $edit_fy->closed ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>
        @else
            <div class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year Begin:</label>
                    <input type="date" name="from_date" value="{{ old('from_date', $next_begin) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('from_date') border-red-500 @enderror">
                    @error('from_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year End:</label>
                    <input type="date" name="to_date" value="{{ old('to_date', $next_end) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('to_date') border-red-500 @enderror">
                    @error('to_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Is Closed:</label>
                    <select name="closed" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
        @endif

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_fy ? 'Update' : 'Add New' }}
            </button>
            @if($edit_fy)
                <a href="{{ route('setup.fiscal-years') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection