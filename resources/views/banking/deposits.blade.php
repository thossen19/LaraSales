@extends('layouts.app')
@section('title', 'Bank Account Deposit Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Bank Account Deposit Entry</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.deposits') }}">
@csrf
<div id="dep_header">
<table class="w-full bg-white shadow rounded-lg mb-6">
<tr>
<td class="p-4 align-top w-1/3">
    <table class="w-full">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Date:</td>
            <td class="py-2">
                <input type="date" name="date_" value="{{ request('date_', $cart['tran_date'] ?? date('Y-m-d')) }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
            <td class="py-2">
                <input type="text" name="ref" value="{{ request('ref', $cart['reference'] ?? '') }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
        </tr>
    </table>
</td>
<td class="p-4 align-top w-1/3">
    <table class="w-full">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">From:</td>
            <td class="py-2">
                <select name="PayType" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    <option value="customer" {{ request('PayType') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="supplier" {{ request('PayType') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="misc" {{ request('PayType') == 'misc' ? 'selected' : '' }}>Miscellaneous</option>
                    <option value="quick" {{ request('PayType') == 'quick' ? 'selected' : '' }}>Quick Entry</option>
                </select>
            </td>
        </tr>
        @php $payType = request('PayType', ''); @endphp
        @if($payType == 'customer')
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Customer:</td>
            <td class="py-2">
                <select name="person_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('person_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Branch:</td>
            <td class="py-2">
                <select name="PersonDetailID" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @php
                        $custBranches = request('person_id') ? \DB::table('customer_branches')->where('customer_id', request('person_id'))->get() : collect();
                    @endphp
                    @foreach($custBranches as $b)
                        <option value="{{ $b->id }}" {{ request('PersonDetailID') == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @elseif($payType == 'supplier')
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Supplier:</td>
            <td class="py-2">
                <select name="person_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('person_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @elseif($payType == 'misc')
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Name:</td>
            <td class="py-2">
                <input type="text" name="person_id" value="{{ request('person_id') }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full">
            </td>
        </tr>
        @elseif($payType == 'quick')
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Type:</td>
            <td class="py-2">
                <select name="person_id" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($quick_entries as $qe)
                        <option value="{{ $qe->id }}" {{ request('person_id') == $qe->id ? 'selected' : '' }}>{{ $qe->description }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @if(request('person_id'))
        @php
            $selQe = \DB::table('quick_entries')->where('id', request('person_id'))->first();
        @endphp
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">{{ $selQe->base_desc ?? 'Amount' }}:</td>
            <td class="py-2">
                <input type="text" name="totamount" value="{{ request('totamount', $selQe->base_amount ?? 0) }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" name="go" value="1" class="ml-2 px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded-md hover:bg-gray-300">Go</button>
            </td>
        </tr>
        @endif
        @endif
    </table>
</td>
<td class="p-4 align-top w-1/3">
    <table class="w-full">
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Into:</td>
            <td class="py-2">
                <select name="bank_account" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select --</option>
                    @foreach($bank_accounts as $ba)
                        <option value="{{ $ba->id }}" {{ request('bank_account') == $ba->id ? 'selected' : '' }}>{{ $ba->bank_account_name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @php
            $selBank = request('bank_account') ? \DB::table('bank_accounts')->where('id', request('bank_account'))->first() : null;
            $bankCurrency = $home_currency;
            if ($selBank) {
                $bankCurrency = $selBank->bank_curr_code ?: $home_currency;
            }
        @endphp
        @if($selBank)
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Bank Balance:</td>
            <td class="py-2 text-sm text-gray-700">0.00</td>
        </tr>
        @endif
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Exchange Rate:</td>
            <td class="py-2 text-sm text-gray-700">
                @if($bankCurrency != $home_currency)
                    {{ $home_currency }}/{{ $bankCurrency }} 1.000000
                @else
                    1.000000
                @endif
            </td>
        </tr>
    </table>
</td>
</tr>
</table>
</div>

<div class="bg-white shadow rounded-lg p-4 mb-6">
    <h3 class="text-lg font-medium text-gray-800 mb-3">Deposit Items</h3>
    <table class="w-full border-collapse" id="items_table">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account Code</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account Description</th>
                @if($use_dimension >= 1)
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 1</th>
                @endif
                @if($use_dimension >= 2)
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimension 2</th>
                @endif
                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                @if(count($cart['gl_items'] ?? []) > 0)
                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @php $totalAmt = 0; @endphp
            @foreach($cart['gl_items'] ?? [] as $idx => $item)
                @php
                    $acc = \DB::table('accounts')->where('code', $item['code_id'])->first();
                    $accName = $acc->name ?? $item['code_id'];
                    $totalAmt += $item['amount'];
                    $displayAmt = -$item['amount']; // negate for display (deposit stores negative)
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 text-sm">{{ $item['code_id'] }}</td>
                    <td class="px-3 py-2 text-sm">{{ $accName }}</td>
                    @if($use_dimension >= 1)
                    <td class="px-3 py-2 text-sm">{{ $item['dimension_id'] ? \DB::table('dimensions')->where('id', $item['dimension_id'])->value('name') : '' }}</td>
                    @endif
                    @if($use_dimension >= 2)
                    <td class="px-3 py-2 text-sm">{{ $item['dimension2_id'] ? \DB::table('dimensions')->where('id', $item['dimension2_id'])->value('name') : '' }}</td>
                    @endif
                    <td class="px-3 py-2 text-sm text-right">{{ number_format($displayAmt, 2) }}</td>
                    <td class="px-3 py-2 text-sm">{{ $item['memo'] }}</td>
                    @if(count($cart['gl_items'] ?? []) > 0)
                    <td class="px-3 py-2 text-center">
                        <button type="submit" name="Edit" value="{{ $idx }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="submit" name="Delete" value="{{ $idx }}" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                    @endif
                </tr>
            @endforeach
            @php
                $isEditing = $edit_index !== null && isset($cart['gl_items'][$edit_index]);
                $editItem = $isEditing ? $cart['gl_items'][$edit_index] : null;
                $editCodeId = request('code_id', $editItem['code_id'] ?? '');
                $editDimId = request('dimension_id', $editItem['dimension_id'] ?? '');
                $editDim2Id = request('dimension2_id', $editItem['dimension2_id'] ?? '');
                $editAmt = request('amount', $editItem ? number_format(abs($editItem['amount']), 2) : '');
                $editMemo = request('LineMemo', $editItem['memo'] ?? '');
                $colspan = 2;
                if ($use_dimension >= 1) $colspan += 1;
                if ($use_dimension >= 2) $colspan += 1;
                $colspan += 1; // Amount
                $colspan += 1; // Memo
            @endphp
            @if($isEditing)
                <tr class="bg-yellow-50">
                    <td colspan="{{ $colspan }}" class="px-3 py-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="code_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-xs">
                                <option value="">-- Select --</option>
                                @foreach($gl_accounts as $ga)
                                    <option value="{{ $ga->code }}" {{ $editCodeId == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                                @endforeach
                            </select>
                            @if($use_dimension >= 1)
                            <select name="dimension_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                @foreach($dimensions as $d)
                                    <option value="{{ $d->id }}" {{ $editDimId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            @if($use_dimension >= 2)
                            <select name="dimension2_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                @foreach($dimensions as $d)
                                    <option value="{{ $d->id }}" {{ $editDim2Id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <input type="text" name="amount" value="{{ $editAmt }}" placeholder="Amount" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24">
                            <input type="text" name="LineMemo" value="{{ $editMemo }}" placeholder="Memo" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-40">
                            <button type="submit" name="UpdateItem" value="1" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md">Update</button>
                            <button type="submit" name="CancelItemChanges" value="1" class="px-3 py-1 bg-gray-200 text-gray-800 text-sm rounded-md">Cancel</button>
                        </div>
                    </td>
                </tr>
            @else
                <tr class="bg-gray-50">
                    <td colspan="{{ $colspan }}" class="px-3 py-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="code_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-xs">
                                <option value="">-- Select --</option>
                                @foreach($gl_accounts as $ga)
                                    <option value="{{ $ga->code }}" {{ request('code_id') == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                                @endforeach
                            </select>
                            @if($use_dimension >= 1)
                            <select name="dimension_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                @foreach($dimensions as $d)
                                    <option value="{{ $d->id }}" {{ request('dimension_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            @if($use_dimension >= 2)
                            <select name="dimension2_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[140px]">
                                <option value="">-- None --</option>
                                @foreach($dimensions as $d)
                                    <option value="{{ $d->id }}" {{ request('dimension2_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <input type="text" name="amount" value="{{ request('amount') }}" placeholder="Amount" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-24">
                            <input type="text" name="LineMemo" value="{{ request('LineMemo') }}" placeholder="Memo" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-40">
                            <button type="submit" name="AddItem" value="1" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md">Add Item</button>
                        </div>
                    </td>
                </tr>
            @endif
            @if(count($cart['gl_items'] ?? []) > 0)
                @php
                    $totalColspan = $colspan - 2;
                    if ($totalColspan < 1) $totalColspan = 1;
                @endphp
                <tr class="font-semibold bg-gray-50">
                    <td colspan="{{ $totalColspan }}" class="px-3 py-2 text-right text-sm">Total</td>
                    <td class="px-3 py-2 text-right text-sm">{{ number_format(abs($totalAmt), 2) }}</td>
                    @if(count($cart['gl_items'] ?? []) > 0)
                    <td class="px-3 py-2"></td>
                    <td class="px-3 py-2"></td>
                    @endif
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div id="footer" class="bg-white shadow rounded-lg p-4 mb-6">
    <table class="w-full">
        @php
            $showSettled = in_array(request('PayType'), ['customer', 'supplier']) && request('person_id');
        @endphp
        @if($showSettled)
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap w-40">Settled {{ request('PayType') == 'customer' ? 'AR' : 'AP' }} Amount:</td>
            <td class="py-2">
                <input type="text" name="settled_amount" value="{{ request('settled_amount', number_format(abs($totalAmt), 2)) }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-500">{{ $bankCurrency }}</span>
            </td>
        </tr>
        @endif
        <tr>
            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap align-top">Memo:</td>
            <td class="py-2">
                <textarea name="memo_" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ request('memo_', $cart['memo_'] ?? '') }}</textarea>
            </td>
        </tr>
    </table>
</div>

<div class="text-center space-x-4">
    <button type="submit" name="Update" value="1" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Update</button>
    <button type="submit" name="Process" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Process Deposit</button>
</div>

</form>
@endsection