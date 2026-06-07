@extends('layouts.app')
@section('title', 'Journal Entry - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Journal Entry</h2>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.journal.entry') }}">
@csrf

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="w-full">
        <tr>
            <td class="p-4 align-top w-1/3">
                <table class="w-full">
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Journal Date:</td>
                        <td class="py-2">
                            <input type="date" name="date_" value="{{ request('date_', $cart['tran_date'] ?? date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Currency:</td>
                        <td class="py-2">
                            <select name="currency" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                @foreach($currencies as $c)
                                    <option value="{{ $c->curr_abrev }}" {{ (request('currency', $cart['currency'] ?: $home_currency)) == $c->curr_abrev ? 'selected' : '' }}>{{ $c->curr_abrev }} - {{ $c->currency }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Exchange Rate:</td>
                        <td class="py-2 text-sm text-gray-700">
                            @php
                                $selCurr = request('currency', $cart['currency'] ?: $home_currency);
                            @endphp
                            @if($selCurr != $home_currency)
                                {{ $home_currency }}/{{ $selCurr }}
                            @else
                                1.000000
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="p-4 align-top w-1/3">
                <table class="w-full">
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Document Date:</td>
                        <td class="py-2">
                            <input type="date" name="doc_date" value="{{ request('doc_date', $cart['doc_date'] ?? date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Event Date:</td>
                        <td class="py-2">
                            <input type="date" name="event_date" value="{{ request('event_date', $cart['event_date'] ?? date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Source ref:</td>
                        <td class="py-2">
                            <input type="text" name="source_ref" value="{{ request('source_ref', $cart['source_ref'] ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </td>
                    </tr>
                </table>
            </td>
            <td class="p-4 align-top w-1/3">
                <table class="w-full">
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Reference:</td>
                        <td class="py-2">
                            <input type="text" name="ref" value="{{ request('ref', $cart['reference'] ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Quick Entry:</td>
                        <td class="py-2">
                            <select name="quick" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">-- Select --</option>
                                @foreach($quick_entries as $qe)
                                    <option value="{{ $qe->id }}" {{ request('quick') == $qe->id ? 'selected' : '' }}>{{ $qe->description }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @if(request('quick'))
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Additional info:</td>
                        <td class="py-2">
                            <input type="text" name="aux_info" value="{{ request('aux_info') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">
                            @php
                                $selQe = \DB::table('quick_entries')->where('id', request('quick'))->first();
                                $qeBaseDesc = $selQe->base_desc ?? 'Amount';
                            @endphp
                            {{ $qeBaseDesc }}:
                        </td>
                        <td class="py-2">
                            <div class="flex items-center gap-2">
                                <input type="text" name="totamount" value="{{ request('totamount', $selQe->base_amount ?? 0) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <button type="submit" name="go" value="1" class="px-3 py-2 bg-gray-200 text-gray-800 text-sm rounded-md hover:bg-gray-300 whitespace-nowrap">Go</button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">Include in tax register:</td>
                        <td class="py-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="taxable_trans" value="1" {{ request('taxable_trans') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Yes</span>
                            </label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="border-b border-gray-200">
        <div class="flex">
            <button type="submit" name="_tabs_sel" value="gl" class="px-6 py-3 text-sm font-medium {{ $tabs_sel == 'gl' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-gray-50' : 'text-gray-500 hover:text-gray-700' }}">GL postings</button>
            <button type="submit" name="_tabs_sel" value="tax" class="px-6 py-3 text-sm font-medium {{ $tabs_sel == 'tax' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-gray-50' : 'text-gray-500 hover:text-gray-700' }}" style="{{ !request('taxable_trans') ? 'display:none' : '' }}">Tax register</button>
        </div>
    </div>

    <div class="p-4">
        @if($tabs_sel == 'gl' || !request('taxable_trans'))
            <div id="tabs-gl">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Rows</h3>
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
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                            @if(count($cart['gl_items'] ?? []) > 0)
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($cart['gl_items'] ?? [] as $idx => $item)
                            @php
                                $acc = \DB::table('accounts')->where('code', $item['code_id'])->first();
                                $accName = $acc->name ?? $item['code_id'];
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
                                <td class="px-3 py-2 text-sm text-right">{{ $item['amount'] > 0 ? number_format($item['amount'], 2) : '' }}</td>
                                <td class="px-3 py-2 text-sm text-right">{{ $item['amount'] < 0 ? number_format(-$item['amount'], 2) : '' }}</td>
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
                            $editAmtDebit = request('AmountDebit', ($editItem && $editItem['amount'] > 0) ? number_format($editItem['amount'], 2) : '');
                            $editAmtCredit = request('AmountCredit', ($editItem && $editItem['amount'] < 0) ? number_format(-$editItem['amount'], 2) : '');
                            $editMemo = request('LineMemo', $editItem['memo'] ?? '');
                            $colspan = 2;
                            if ($use_dimension >= 1) $colspan += 1;
                            if ($use_dimension >= 2) $colspan += 1;
                        @endphp
                        @if($isEditing)
                            <tr class="bg-yellow-50">
                                <td colspan="{{ $colspan }}" class="px-3 py-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <select name="code_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[200px]">
                                            <option value="">[Select account]</option>
                                            @foreach($gl_accounts as $ga)
                                                <option value="{{ $ga->code }}" {{ $editCodeId == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($use_dimension >= 1)
                                        <select name="dimension_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[130px]">
                                            <option value="">-- None --</option>
                                            @foreach($dimensions as $d)
                                                <option value="{{ $d->id }}" {{ $editDimId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                        @endif
                                        @if($use_dimension >= 2)
                                        <select name="dimension2_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[130px]">
                                            <option value="">-- None --</option>
                                            @foreach($dimensions as $d)
                                                <option value="{{ $d->id }}" {{ $editDim2Id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                        @endif
                                        <input type="text" name="AmountDebit" value="{{ $editAmtDebit }}" placeholder="Debit" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20">
                                        <input type="text" name="AmountCredit" value="{{ $editAmtCredit }}" placeholder="Credit" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20">
                                        <input type="text" name="LineMemo" value="{{ $editMemo }}" placeholder="Memo" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-36">
                                        <button type="submit" name="UpdateItem" value="1" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md">Update</button>
                                        <button type="submit" name="CancelItemChanges" value="1" class="px-3 py-1 bg-gray-200 text-gray-800 text-sm rounded-md">Cancel</button>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr class="bg-gray-50">
                                <td colspan="{{ $colspan }}" class="px-3 py-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <select name="code_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[200px]">
                                            <option value="">[Select account]</option>
                                            @foreach($gl_accounts as $ga)
                                                <option value="{{ $ga->code }}" {{ request('code_id') == $ga->code ? 'selected' : '' }}>{{ $ga->code }} {{ $ga->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($use_dimension >= 1)
                                        <select name="dimension_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[130px]">
                                            <option value="">-- None --</option>
                                            @foreach($dimensions as $d)
                                                <option value="{{ $d->id }}" {{ request('dimension_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                        @endif
                                        @if($use_dimension >= 2)
                                        <select name="dimension2_id" class="border border-gray-300 rounded-md px-2 py-1 text-sm max-w-[130px]">
                                            <option value="">-- None --</option>
                                            @foreach($dimensions as $d)
                                                <option value="{{ $d->id }}" {{ request('dimension2_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                        @endif
                                        <input type="text" name="AmountDebit" value="{{ request('AmountDebit') }}" placeholder="Debit" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20">
                                        <input type="text" name="AmountCredit" value="{{ request('AmountCredit') }}" placeholder="Credit" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-20">
                                        <input type="text" name="LineMemo" value="{{ request('LineMemo') }}" placeholder="Memo" class="border border-gray-300 rounded-md px-2 py-1 text-sm w-36">
                                        <button type="submit" name="AddItem" value="1" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md">Add Item</button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @if(count($cart['gl_items'] ?? []) > 0)
                            @php
                                $totalColspan = $colspan;
                            @endphp
                            <tr class="font-semibold bg-gray-50">
                                <td colspan="{{ $totalColspan }}" class="px-3 py-2 text-right text-sm">Total</td>
                                <td class="px-3 py-2 text-right text-sm">{{ number_format($total_debit, 2) }}</td>
                                <td class="px-3 py-2 text-right text-sm">{{ number_format($total_credit, 2) }}</td>
                                @if(count($cart['gl_items'] ?? []) > 0)
                                <td class="px-3 py-2"></td>
                                <td class="px-3 py-2" colspan="2"></td>
                                @endif
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="mt-4">
                    <table class="w-full">
                        <tr>
                            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap align-top w-16">Memo:</td>
                            <td class="py-2">
                                <textarea name="memo_" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">{{ request('memo_', $cart['memo_'] ?? '') }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            <div id="tabs-tax">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Tax register record</h3>
                <div class="mb-4">
                    <table class="w-1/2">
                        <tr>
                            <td class="py-2 pr-3 text-sm font-medium text-gray-700 whitespace-nowrap">VAT date:</td>
                            <td class="py-2">
                                <input type="date" name="tax_date" value="{{ request('tax_date', date('Y-m-d')) }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </td>
                        </tr>
                    </table>
                </div>
                <table class="w-3/5 border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Input Tax</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Output Tax</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Net amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tax_types as $tax)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-sm">{{ $tax->name }} {{ $tax->rate }}%</td>
                                <td class="px-3 py-2 text-sm text-right">0.00</td>
                                <td class="px-3 py-2 text-sm text-right">0.00</td>
                                <td class="px-3 py-2 text-sm">
                                    <input type="text" name="net_amount_{{ $tax->id }}" value="{{ request('net_amount_'.$tax->id, '0') }}" class="w-24 border border-gray-300 rounded-md px-2 py-1 text-sm text-right">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="text-center">
    <button type="submit" name="Process" value="1" class="px-8 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Process Journal Entry</button>
</div>

</form>
@endsection