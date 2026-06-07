@extends('layouts.app')
@section('title', 'Payment Terms - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Payment Terms</h2>
    <p class="mt-2 text-gray-600">Manage payment terms for customers and suppliers.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.payment-terms') }}" class="mb-4">
    @csrf
    <input type="hidden" name="action" value="toggle_show_inactive">
    <label class="flex items-center text-sm text-gray-700 cursor-pointer">
        <input type="checkbox" name="show_inactive" value="1" {{ $show_inactive ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-2">Show also inactive</span>
    </label>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due After/Days</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inactive</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($terms_list as $pt)
                @php
                    $type_val = $pt->day_in_following_month != 0 ? $PTT_FOLLOWING : ($pt->days_before_due < 0 ? $PTT_PRE : ($pt->days_before_due ? $PTT_DAYS : $PTT_CASH));
                    $days_val = $pt->day_in_following_month ?: $pt->days_before_due;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $pt->terms }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $pterm_types[$type_val] }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        {{ $type_val == $PTT_DAYS ? $days_val . ' days' : ($type_val == $PTT_FOLLOWING ? $days_val : 'N/A') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.payment-terms') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="toggle_inactive">
                            <input type="hidden" name="selected_id" value="{{ $pt->terms_indicator }}">
                            <button type="submit" class="text-sm {{ $pt->inactive ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                {{ $pt->inactive ? 'Yes' : 'No' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.payment-terms') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="selected_id" value="{{ $pt->terms_indicator }}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('setup.payment-terms') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment term?');">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="selected_id" value="{{ $pt->terms_indicator }}">
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No payment terms defined yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $edit_pt ? 'Edit Payment Terms' : 'Add New Payment Terms' }}
    </h3>
    <form method="POST" action="{{ route('setup.payment-terms') }}">
        @csrf
        <input type="hidden" name="action" value="{{ $edit_pt ? 'update' : 'add' }}">
        @if($edit_pt)
            <input type="hidden" name="selected_id" value="{{ $edit_pt->terms_indicator }}">
        @endif

        <div class="space-y-4 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Terms Description:</label>
                <input type="text" name="terms" value="{{ old('terms', $edit_pt->terms ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('terms') border-red-500 @enderror">
                @error('terms') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment type:</label>
                <select name="type" id="payment-type-select" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($pterm_types as $val => $label)
                        <option value="{{ $val }}" {{ (old('type', $edit_type ?? $PTT_CASH) == $val) ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div id="days-field" class="{{ (old('type', $edit_type ?? $PTT_CASH) == $PTT_FOLLOWING || old('type', $edit_type ?? $PTT_CASH) == $PTT_DAYS) ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Days (Or Day In Following Month):</label>
                <input type="number" name="DayNumber" value="{{ old('DayNumber', $edit_days ?? 0) }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            @if(old('type', $edit_type ?? $PTT_CASH) != $PTT_FOLLOWING && old('type', $edit_type ?? $PTT_CASH) != $PTT_DAYS)
                <input type="hidden" name="DayNumber" value="0">
            @endif
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $edit_pt ? 'Update' : 'Add New' }}
            </button>
            @if($edit_pt)
                <a href="{{ route('setup.payment-terms') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-150">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('payment-type-select')?.addEventListener('change', function() {
    var daysField = document.getElementById('days-field');
    var val = parseInt(this.value);
    if (val === {{ $PTT_DAYS }} || val === {{ $PTT_FOLLOWING }}) {
        daysField.classList.remove('hidden');
    } else {
        daysField.classList.add('hidden');
    }
});
</script>
@endpush