@extends('layouts.app')
@section('title', 'Void a Transaction - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Void a Transaction</h2>
    <p class="mt-2 text-gray-600">Void or cancel existing transactions.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{!! $message !!}</div>
@endif
@if($error_msg)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error_msg }}</div>
@endif

<form method="GET" action="{{ route('setup.void-transaction') }}">
    <table class="mb-4">
        <tr>
            <td class="pr-3 text-sm text-gray-700 font-medium">Transaction Type:</td>
            <td class="pr-3">
                <select name="filterType" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @foreach($systypes as $code => $label)
                        @if(!in_array($code, [18, 30, 32, 35, 50, 51]))
                            <option value="{{ $code }}" {{ $filterType == $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td class="pr-3 text-sm text-gray-700 font-medium">from #:</td>
            <td class="pr-3">
                <input type="text" name="FromTransNo" value="{{ $fromNo }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </td>
            <td class="pr-3 text-sm text-gray-700 font-medium">to #:</td>
            <td class="pr-3">
                <input type="text" name="ToTransNo" value="{{ $toNo }}" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </td>
            <td>
                <button type="submit" name="ProcessSearch" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Search</button>
            </td>
        </tr>
    </table>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Date</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">GL</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Select</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($results as $r)
                @php $is_selected = $r['trans_no'] == $selected_id; @endphp
                <tr class="hover:bg-gray-50 {{ $is_selected ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-3 text-sm text-indigo-600 whitespace-nowrap">
                        <a href="#" class="hover:underline">{{ $r['trans_no'] }}</a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $r['ref'] ?? '' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $r['trans_date'] }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm">GL</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.void-transaction', ['filterType' => $filterType, 'FromTransNo' => $fromNo, 'ToTransNo' => $toNo, 'selected_id' => $r['trans_no']]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Select</a>
                    </td>
                </tr>
                @if($is_selected)
                    <tr><td colspan="5" class="px-4 py-2 text-xs text-yellow-700 bg-yellow-50 italic">Marked transactions will be voided.</td></tr>
                @endif
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        @if(request()->has('ProcessSearch'))
                            No transactions found.
                        @else
                            Select transaction type and press Search button.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<form method="POST" action="{{ route('setup.void-transaction', ['filterType' => $filterType, 'FromTransNo' => $fromNo, 'ToTransNo' => $toNo, 'selected_id' => $selected_id]) }}">
    @csrf

    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <table class="w-full max-w-lg">
            <tr>
                <td class="py-2 pr-4 text-sm font-medium text-gray-700 w-48">Transaction #:</td>
                <td class="py-2 text-sm text-gray-900">
                    @if($selected_id != -1 && $selected_id !== '')
                        {{ $selected_id }}
                        <input type="hidden" name="trans_no" value="{{ $selected_id }}">
                    @else
                        <input type="text" name="trans_no" value="" class="w-32 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @endif
                </td>
            </tr>
            <tr>
                <td class="py-2 pr-4 text-sm font-medium text-gray-700">Voiding Date:</td>
                <td class="py-2">
                    <input type="date" name="date_" value="{{ old('date_', $today) }}" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </td>
            </tr>
            <tr>
                <td class="py-2 pr-4 text-sm font-medium text-gray-700 align-top">Memo:</td>
                <td class="py-2">
                    <textarea name="memo_" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">{{ old('memo_') }}</textarea>
                </td>
            </tr>
        </table>
    </div>

    @if($confirm)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-yellow-800 font-medium">Are you sure you want to void this transaction? This action cannot be undone.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" name="confirmed" value="1" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition">Proceed</button>
            <a href="{{ route('setup.void-transaction', ['filterType' => $filterType, 'FromTransNo' => $fromNo, 'ToTransNo' => $toNo, 'cancel' => 1]) }}" class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition">Cancel</a>
        </div>
        <input type="hidden" name="action" value="void">
        <input type="hidden" name="trans_no" value="{{ $selected_id }}">
    @elseif($selected_id !== -1 && $selected_id !== '')
        <button type="submit" name="action" value="void" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Void Transaction</button>
    @else
        <div class="text-sm text-gray-500 italic">Please select a transaction from the list above.</div>
    @endif
</form>
@endsection