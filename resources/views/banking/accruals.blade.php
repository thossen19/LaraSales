@extends('layouts.app')
@section('title', 'Revenue / Cost Accruals - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Revenue / Cost Accruals</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.accruals') }}">
@csrf

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <table class="w-full max-w-2xl">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Date:</td>
            <td class="py-2">
                <input type="date" name="date_" value="{{ request('date_', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Accrued Balance Account:</td>
            <td class="py-2">
                <select name="acc_act" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                    @foreach($gl_accounts as $ga)
                        <option value="{{ $ga->code }}" {{ request('acc_act') == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Revenue / Cost Account:</td>
            <td class="py-2">
                <select name="res_act" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">-- Select --</option>
                    @foreach($gl_accounts as $ga)
                        <option value="{{ $ga->code }}" {{ request('res_act') == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @if($use_dimension >= 1)
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension:</td>
            <td class="py-2">
                <select name="dimension_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ request('dimension_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @else
        <input type="hidden" name="dimension_id" value="">
        @endif
        @if($use_dimension >= 2)
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Dimension 2:</td>
            <td class="py-2">
                <select name="dimension2_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">-- None --</option>
                    @foreach($dimensions as $d)
                        <option value="{{ $d->id }}" {{ request('dimension2_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @else
        <input type="hidden" name="dimension2_id" value="">
        @endif
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Amount:</td>
            <td class="py-2">
                <input type="text" name="amount" value="{{ request('amount') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Frequency:</td>
            <td class="py-2">
                <select name="freq" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="1" {{ request('freq') == '1' ? 'selected' : '' }}>Weekly</option>
                    <option value="2" {{ request('freq') == '2' ? 'selected' : '' }}>Bi-weekly</option>
                    <option value="3" {{ request('freq') == '3' ? 'selected' : '' }}>Monthly</option>
                    <option value="4" {{ request('freq') == '4' ? 'selected' : '' }}>Quarterly</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Periods:</td>
            <td class="py-2">
                <input type="text" name="periods" value="{{ request('periods', '12') }}" maxlength="3" class="w-20 border border-gray-300 rounded-md px-3 py-2 text-sm">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap align-top">Memo:</td>
            <td class="py-2">
                <textarea name="memo_" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">{{ request('memo_') }}</textarea>
            </td>
        </tr>
    </table>
</div>

@if(count($previewRows) > 0)
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    @php
        $firstCols = ['Date', 'Account'];
        $dimCols = [];
        if ($use_dimension >= 2) {
            $dimCols = ['Dimension 1', 'Dimension 2'];
        } elseif ($use_dimension >= 1) {
            $dimCols = ['Dimension'];
        }
        $remainingCols = ['Debit', 'Credit', 'Memo'];
        $allCols = array_merge($firstCols, $dimCols, $remainingCols);
    @endphp
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50">
                @foreach($allCols as $col)
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($previewRows as $row)
                @php
                    $accName = \DB::table('accounts')->where('code', $row['acc_act'])->value('name') ?? '';
                    $resName = \DB::table('accounts')->where('code', $row['res_act'])->value('name') ?? '';
                    $dim1Name = $row['dim_id'] ? \DB::table('dimensions')->where('id', $row['dim_id'])->value('name') : '';
                    $dim2Name = $row['dim2_id'] ? \DB::table('dimensions')->where('id', $row['dim2_id'])->value('name') : '';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-sm">{{ $row['date'] }}</td>
                    <td class="px-3 py-2 text-sm">{{ $row['acc_act'] }} {{ $accName }}</td>
                    @if($use_dimension >= 1)
                    <td class="px-3 py-2 text-sm">{{ '' }}</td>
                    @endif
                    @if($use_dimension >= 2)
                    <td class="px-3 py-2 text-sm">{{ '' }}</td>
                    @endif
                    <td class="px-3 py-2 text-sm text-right">{{ $row['acc_amount'] < 0 ? number_format(-$row['acc_amount'], 2) : '0.00' }}</td>
                    <td class="px-3 py-2 text-sm text-right">{{ $row['acc_amount'] > 0 ? number_format($row['acc_amount'], 2) : '0.00' }}</td>
                    <td class="px-3 py-2 text-sm">{{ $row['memo'] }}</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-sm">{{ $row['date'] }}</td>
                    <td class="px-3 py-2 text-sm">{{ $row['res_act'] }} {{ $resName }}</td>
                    @if($use_dimension >= 1)
                    <td class="px-3 py-2 text-sm">{{ $dim1Name }}</td>
                    @endif
                    @if($use_dimension >= 2)
                    <td class="px-3 py-2 text-sm">{{ $dim2Name }}</td>
                    @endif
                    <td class="px-3 py-2 text-sm text-right">{{ $row['res_amount'] < 0 ? number_format(-$row['res_amount'], 2) : '0.00' }}</td>
                    <td class="px-3 py-2 text-sm text-right">{{ $row['res_amount'] > 0 ? number_format($row['res_amount'], 2) : '0.00' }}</td>
                    <td class="px-3 py-2 text-sm">{{ $row['memo'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="text-center mb-4 text-sm text-gray-600">Showing GL Transactions.</div>
@endif

<div class="text-center space-x-4">
    <button type="submit" name="show" value="1" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Show GL Rows</button>
    <button type="submit" name="go" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition" onclick="return confirm('Are you sure you want to post accruals?')">Process Accruals</button>
</div>

</form>
@endsection