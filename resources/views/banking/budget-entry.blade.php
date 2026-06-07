@extends('layouts.app')
@section('title', 'Budget Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Budget Entry</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.budget-entry') }}">
@csrf

<div class="bg-white shadow rounded-lg p-4 mb-6">
    <table class="w-full max-w-2xl">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Fiscal Year:</td>
            <td class="py-2">
                <select name="fyear" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    @foreach($fiscal_years as $fy)
                        <option value="{{ $fy->id }}" {{ $fyear == $fy->id ? 'selected' : '' }}>{{ $fy->begin->format('Y-m-d') }} - {{ $fy->end->format('Y-m-d') }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="begin" value="{{ $begin }}">
                <input type="hidden" name="end" value="{{ $end }}">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Account Code:</td>
            <td class="py-2">
                <select name="account" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                    @foreach($gl_accounts as $ga)
                        <option value="{{ $ga->code }}" {{ $account == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @if($use_dimension >= 1)
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 1:</td>
            <td class="py-2">
                <select name="dim1" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="0">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $dim1 == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @else
        <input type="hidden" name="dim1" value="0">
        @endif
        @if($use_dimension >= 2)
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 2:</td>
            <td class="py-2">
                <select name="dim2" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="0">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ $dim2 == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @else
        <input type="hidden" name="dim2" value="0">
        @endif
        <tr>
            <td></td>
            <td class="py-2">
                <button type="submit" name="submit" value="1" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Get</button>
            </td>
        </tr>
    </table>
</div>

@if($account)
<div class="bg-white shadow rounded-lg overflow-hidden mb-6" id="budget_tbl">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                @if($showdims)
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Dim. incl.</th>
                @endif
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Last Year</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($month_rows as $row)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-900">{{ $row['date'] }}</td>
                <td class="px-4 py-3 text-sm text-right">
                    <input type="text" name="{{ $row['input_name'] }}" value="{{ $row['budget_amount'] }}" class="w-32 border border-gray-300 rounded-md px-2 py-1 text-sm text-right">
                </td>
                @if($showdims)
                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $row['dim_incl'] }}</td>
                @endif
                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $row['last_year'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-50 font-semibold">
                <td class="px-4 py-3 text-sm text-gray-900">Total</td>
                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ number_format($total, 0) }}</td>
                @if($showdims)
                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ number_format($btotal, 0) }}</td>
                @endif
                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ number_format($ltotal, 0) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="text-center space-x-4">
    <button type="submit" name="update" value="1" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Update</button>
    <button type="submit" name="add" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Save</button>
    <button type="submit" name="delete" value="1" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition">Delete</button>
</div>
@endif

</form>
@endsection