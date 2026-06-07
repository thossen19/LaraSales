@extends('layouts.app')
@section('title', 'Quick Entries - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Quick Entries</h2>
    <p class="mt-2 text-gray-600">Manage quick journal entries.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="POST" action="{{ route('banking.quick-entries') }}">
@csrf

{{-- Existing Quick Entries Table --}}
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($entries as $e)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $e->description }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $qe_types[$e->type] ?? $e->type }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $e->usage }}</td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Edit" onclick="document.getElementById('selected_id').value='{{ $e->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode" value="Delete" onclick="document.getElementById('selected_id').value='{{ $e->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No quick entries defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Quick Entry Editor --}}
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
        {{ $selected_id ? 'Edit Quick Entry' : 'Add New Quick Entry' }}
    </h3>

    <input type="hidden" name="selected_id" id="selected_id" value="{{ $selected_id }}">
    <input type="hidden" name="selected_id2" id="selected_id2" value="{{ $selected_id2 }}">

    <div class="space-y-4 max-w-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
            <input type="text" name="description" value="{{ $post['description'] }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Usage:</label>
            <input type="text" name="usage" value="{{ $post['usage'] }}" maxlength="120" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Entry Type:</label>
            <select name="type" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @foreach($qe_types as $val => $label)
                    <option value="{{ $val }}" {{ $post['type'] == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if($post['type'] == 3 || $post['type'] == '3')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Balance Based:</label>
            <select name="bal_type" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="0" {{ !$post['bal_type'] ? 'selected' : '' }}>No</option>
                <option value="1" {{ $post['bal_type'] ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        @endif

        @if(($post['type'] == 3 || $post['type'] == '3') && $post['bal_type'])
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Period:</label>
            <select name="base_amount" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="0" {{ $post['base_amount'] == 0 ? 'selected' : '' }}>Monthly</option>
                <option value="1" {{ $post['base_amount'] == 1 ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account:</label>
            <select name="base_desc" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                <option value="">-- Select --</option>
                @foreach($allAccounts as $acc)
                    <option value="{{ $acc->code }}" {{ $post['base_desc'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                @endforeach
            </select>
        </div>
        @else
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Base Amount Description:</label>
            <input type="text" name="base_desc" value="{{ $post['base_desc'] }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Default Base Amount:</label>
            <input type="text" name="base_amount" value="{{ number_format($post['base_amount'], 2, '.', '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        @endif
    </div>

    <div class="pt-4 mt-4 border-t border-gray-200">
        @if($selected_id)
            <button type="submit" name="Mode" value="UPDATE_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Update</button>
            <button type="submit" name="Mode" value="RESET" class="px-6 py-2 ml-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 transition">Cancel</button>
        @else
            <button type="submit" name="Mode" value="ADD_ITEM" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">Add New</button>
        @endif
    </div>
</div>

{{-- Quick Entry Lines --}}
@if($selected_id)
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Quick Entry Lines - {{ $post['description'] ?: ($edit_entry->description ?? '') }}</h3>

    <table class="min-w-full divide-y divide-gray-200 mb-4">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account/Tax Type</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memo</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($lines as $l)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $quick_actions[$l->action] ?? $l->action }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        @if(in_array(substr($l->action, 0, 1), ['t', 'T']))
                            {{ $l->taxType->name ?? $l->dest_id }}
                        @else
                            {{ $l->dest_id }} {{ $l->glAccount->name ?? '' }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 text-right">
                        @if(substr($l->action, 0, 1) == '%')
                            {{ number_format($l->amount, 4) }}%
                        @elseif($l->action == '=')
                            &mdash;
                        @else
                            {{ number_format($l->amount, 2) }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $l->memo }}</td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode2" value="BEd" onclick="document.getElementById('selected_id2').value='{{ $l->id }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="submit" name="Mode2" value="BDel" onclick="document.getElementById('selected_id2').value='{{ $l->id }}';return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">No lines defined.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Line Editor --}}
    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mt-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ $selected_id2 ? 'Edit Line' : 'Add New Line' }}</h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Posted:</label>
                <select name="actn" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select --</option>
                    @foreach($quick_actions as $val => $label)
                        <option value="{{ $val }}" {{ $line_post['actn'] == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                @if(in_array(substr($line_post['actn'], 0, 1), ['t', 'T']))
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax Type:</label>
                    <select name="dest_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select --</option>
                        @foreach($taxTypes as $tx)
                            <option value="{{ $tx->id }}" {{ $line_post['dest_id'] == $tx->id ? 'selected' : '' }}>{{ $tx->name }} ({{ $tx->rate }}%)</option>
                        @endforeach
                    </select>
                @else
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account:</label>
                    <select name="dest_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">-- Select --</option>
                        @foreach($allAccounts as $acc)
                            <option value="{{ $acc->code }}" {{ $line_post['dest_id'] == $acc->code ? 'selected' : '' }}>{{ $acc->code }} {{ $acc->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            @if($line_post['actn'] != '=')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ substr($line_post['actn'], 0, 1) == '%' ? 'Part (%)' : 'Amount' }}:
                </label>
                <input type="text" name="amount" value="{{ $line_post['amount'] > 0 ? $line_post['amount'] : '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="0">
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Line Memo:</label>
                <input type="text" name="memo" value="{{ $line_post['memo'] }}" maxlength="256" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex gap-2">
            @if($selected_id2)
                <button type="submit" name="Mode2" value="UPDATE_ITEM2" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 text-sm">Update</button>
                <button type="submit" name="Mode2" value="RESET2" class="px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-md hover:bg-gray-300 text-sm">Cancel</button>
            @else
                <button type="submit" name="Mode2" value="ADD_ITEM2" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 text-sm">Add New</button>
            @endif
        </div>
    </div>
</div>
@endif

</form>
@endsection