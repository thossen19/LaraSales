@extends('layouts.app')
@section('title', 'Attach Documents - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Attach Documents</h2>
    <p class="mt-2 text-gray-600">Attach documents to transactions, customers, suppliers, items and bank accounts.</p>
</div>

@if($message)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $message }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
@endif

<form method="GET" action="{{ route('setup.attach-documents') }}">
    <table class="mb-4">
        <tr>
            <td class="pr-3 text-sm text-gray-700 font-medium">Type:</td>
            <td class="pr-3">
                <select name="filterType" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @foreach($systypes as $code => $label)
                        <option value="{{ $code }}" {{ $filterType == $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            @if(in_array($filterType, [50, 51, 52, 53, 54]))
                <td class="pl-3 text-sm text-gray-700 font-medium">
                    @if($filterType == 50)
                        Select a customer:
                    @elseif($filterType == 51)
                        Select a supplier:
                    @elseif(in_array($filterType, [52, 53]))
                        Select an Item:
                    @elseif($filterType == 54)
                        Select a Bank Account:
                    @endif
                </td>
                <td class="pl-3">
                    @if($filterType == 50)
                        <select name="trans_no" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ $trans_no == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    @elseif($filterType == 51)
                        <select name="trans_no" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $trans_no == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    @elseif(in_array($filterType, [52, 53]))
                        <select name="trans_no" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach($items as $it)
                                <option value="{{ $it->id }}" {{ $trans_no == $it->id ? 'selected' : '' }}>{{ $it->code }} - {{ $it->name }}</option>
                            @endforeach
                        </select>
                    @elseif($filterType == 54)
                        <select name="trans_no" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- None --</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}" {{ $trans_no == $a->id ? 'selected' : '' }}>{{ $a->code }} {{ $a->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </td>
            @else
                <td class="pl-3 text-sm text-gray-700 font-medium">Transaction #:</td>
                <td class="pl-3">
                    <input type="text" name="trans_no" value="{{ $trans_no }}" class="w-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </td>
            @endif
            <td class="pl-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">Search</button>
            </td>
        </tr>
    </table>
</form>

<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doc Title</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filename</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Size</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Filetype</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Doc Date</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">Edit</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">View</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Downld</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-14">Delete</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($attachments as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-indigo-600 whitespace-nowrap">
                        @if(in_array($filterType, [50, 51, 52, 53, 54]))
                            {{ $a->id }}
                        @else
                            <a href="#" class="hover:underline">{{ $a->trans_no }}</a>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $a->description }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 truncate max-w-[200px]">{{ $a->filename }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $a->filesize }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 truncate max-w-[120px]">{{ $a->filetype }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $a->tran_date }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.attach-documents', ['Mode' => 'Edit', 'selected_id' => $a->id, 'filterType' => $filterType, 'trans_no' => $trans_no]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.attach-documents', ['vw' => $a->id, 'filterType' => $filterType, 'trans_no' => $trans_no]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.attach-documents', ['dl' => $a->id, 'filterType' => $filterType, 'trans_no' => $trans_no]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Download</a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('setup.attach-documents', ['Mode' => 'Delete', 'selected_id' => $a->id, 'filterType' => $filterType, 'trans_no' => $trans_no]) }}" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500">No attachments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<form method="POST" action="{{ route('setup.attach-documents', ['filterType' => $filterType, 'trans_no' => $trans_no]) }}" enctype="multipart/form-data">
    @csrf

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
            {{ $edit_row ? 'Edit Attachment' : 'Add New Attachment' }}
        </h3>

        <div class="space-y-4 max-w-lg">
            @if(!in_array($filterType, [50, 51, 52, 53, 54]))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction #:</label>
                    <input type="text" name="trans_no" value="{{ old('trans_no', $edit_row->trans_no ?? $trans_no) }}" class="w-32 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
            @elseif($edit_row)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction #:</label>
                    <span class="py-2 text-sm text-gray-800 inline-block">{{ $edit_row->trans_no }}</span>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Doc Title:</label>
                <input type="text" name="description" value="{{ old('description', $edit_row->description ?? '') }}" maxlength="60" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            @if($edit_row)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current file: {{ $edit_row->filename }}</label>
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attached File:</label>
                <input type="file" name="filename" class="text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200 flex items-center gap-3">
            <button type="submit" name="Mode" value="{{ $edit_row ? 'UPDATE_ITEM' : 'ADD_ITEM' }}" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition">
                {{ $edit_row ? 'Update' : 'Add New' }}
            </button>
            @if($edit_row)
                <a href="{{ route('setup.attach-documents', ['filterType' => $filterType, 'trans_no' => $trans_no]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition">Cancel</a>
            @endif
        </div>
    </div>
</form>
@endsection