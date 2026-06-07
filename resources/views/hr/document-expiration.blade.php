@extends('layouts.app')
@section('title', 'Employee Attach Documents')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Employee Attach Documents</h2>
</div>

@if($msg)
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ $msg }}</div>
@endif
@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{!! $error !!}</div>
@endif

@if(!$has_doc_types)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">There are no <b>Document Types</b> defined in the system</div>
@endif

<form method="post" action="{{ url()->current() }}" enctype="multipart/form-data">
    @csrf

    {{-- Viewing Controls --}}
    <table class="table-auto border-collapse bg-white shadow rounded-lg mb-4 w-full">
        @if(empty($view_mode))
        <tr>
            <td class="p-2">
                <select name="emp_id" class="border px-2 py-1 min-w-[200px]">
                    <option value="">Select employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected($emp_id == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        @else
        <tr>
            <td class="p-2">
                <table class="w-full">
                    <tr>
                        <td class="p-1"><input type="text" name="string" value="{{ request('string') }}" size="30" placeholder="Enter search string" class="border px-2 py-1"></td>
                        <td class="p-1">
                            <select name="emp_id" class="border px-2 py-1 min-w-[150px]">
                                <option value="">All employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected(request('emp_id') == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1">
                            <select name="type_id" class="border px-2 py-1 min-w-[150px]">
                                <option value="">All document type</option>
                                @foreach($doc_types as $dt)
                                    <option value="{{ $dt->type_id }}" @selected(request('type_id') == $dt->type_id)>{{ $dt->type_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-1"><label><input type="checkbox" name="alert" value="1" {{ request('alert') ? 'checked' : '' }}> Alert</label></td>
                        <td class="p-1"><label><input type="checkbox" name="no_alert" value="1" {{ request('no_alert') ? 'checked' : '' }}> Not Alert</label></td>
                    </tr>
                    <tr>
                        <td class="p-1">Expired: <input type="text" name="expired_from" value="{{ request('expired_from') }}" size="10" class="border px-2 py-1"></td>
                        <td class="p-1">To: <input type="text" name="expired_to" value="{{ request('expired_to') }}" size="10" class="border px-2 py-1"></td>
                        <td class="p-1">Issued: <input type="text" name="issue_from" value="{{ request('issue_from') }}" size="10" class="border px-2 py-1"></td>
                        <td class="p-1">To: <input type="text" name="issue_to" value="{{ request('issue_to') }}" size="10" class="border px-2 py-1"></td>
                        <td class="p-1"><input type="submit" name="Search" value="Search" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"></td>
                    </tr>
                </table>
            </td>
        </tr>
        @endif
    </table>

    {{-- Documents Table --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto mb-4">
        <table class="table-auto w-full text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Doc No</th>
                    <th class="border px-2 py-1">Document Type</th>
                    <th class="border px-2 py-1">Document Title</th>
                    <th class="border px-2 py-1">Issue Date</th>
                    <th class="border px-2 py-1">Expiry Date</th>
                    <th class="border px-2 py-1">Alert</th>
                    <th class="border px-2 py-1">Filename</th>
                    <th class="border px-2 py-1">Size</th>
                    <th class="border px-2 py-1">Filetype</th>
                    <th class="border px-2 py-1"></th>
                    <th class="border px-2 py-1"></th>
                    <th class="border px-2 py-1"></th>
                    @if(empty($view_mode))
                        <th class="border px-2 py-1"></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    @php
                        $today = strtotime('today');
                        $expiry = strtotime($doc->expiry_date);
                        $is_expired = $expiry < $today && $doc->alert;
                        $alert_from = $doc->notify_before ? date('Y-m-d', strtotime($doc->expiry_date . ' - ' . $doc->notify_before . ' days')) : $doc->expiry_date;
                        $is_warning = $doc->alert && strtotime($alert_from) <= $today && $today <= $expiry;
                    @endphp
                    <tr class="{{ $is_expired ? 'bg-red-100' : ($is_warning ? 'bg-yellow-100' : '') }}">
                        <td class="border px-2 py-1 text-center">{{ $doc->id }}</td>
                        <td class="border px-2 py-1">{{ $doc->type_name }}</td>
                        <td class="border px-2 py-1">{{ $doc->description }}</td>
                        <td class="border px-2 py-1">{{ date('d/m/Y', strtotime($doc->issue_date)) }}</td>
                        <td class="border px-2 py-1">{{ date('d/m/Y', strtotime($doc->expiry_date)) }}</td>
                        <td class="border px-2 py-1 text-center">{{ $doc->alert ? 'Alert' : '' }}</td>
                        <td class="border px-2 py-1">{{ $doc->filename }}</td>
                        <td class="border px-2 py-1">{{ $doc->filesize }}</td>
                        <td class="border px-2 py-1">{{ $doc->filetype }}</td>
                        <td class="border px-2 py-1 text-center">
                            @if(!empty($view_mode))
                                <a href="{{ route('hr.document-expiration') }}?EmpId={{ $doc->emp_id }}&DocId={{ $doc->id }}" class="text-blue-600 hover:text-blue-800 text-xs">Edit</a>
                            @else
                                <button type="submit" name="Edit{{ $doc->id }}" value="{{ $doc->id }}" class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                            @endif
                        </td>
                        <td class="border px-2 py-1 text-center">
                            <button type="submit" name="view{{ $doc->id }}" value="{{ $doc->id }}" class="text-green-600 hover:text-green-800 text-xs">View</button>
                        </td>
                        <td class="border px-2 py-1 text-center">
                            <button type="submit" name="download{{ $doc->id }}" value="{{ $doc->id }}" class="text-purple-600 hover:text-purple-800 text-xs">Download</button>
                        </td>
                        @if(empty($view_mode))
                            <td class="border px-2 py-1 text-center">
                                <button type="submit" name="Delete{{ $doc->id }}" value="{{ $doc->id }}" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('Delete?')">Delete</button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="border px-2 py-1 text-center text-gray-500" colspan="{{ empty($view_mode) ? 13 : 12 }}">No documents found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(empty($view_mode))
        {{-- Entry Form --}}
        <div class="bg-white shadow rounded-lg p-4">
            <table class="table-auto border-collapse">
                @if($selected_id && $selected_id != -1)
                    <tr>
                        <td class="p-1 text-right font-semibold">Document Number:</td>
                        <td class="p-1">&nbsp;&nbsp;{{ $selected_id }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="p-1 text-right font-semibold">Document type:</td>
                    <td class="p-1">
                        <select name="type_id" class="border px-2 py-1 min-w-[200px]">
                            <option value="">Select document type</option>
                            @foreach($doc_types as $dt)
                                <option value="{{ $dt->type_id }}" @selected(old('type_id', $edit_doc->type_id ?? '') == $dt->type_id)>{{ $dt->type_name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Document title:</td>
                    <td class="p-1"><input type="text" name="doc_title" value="{{ old('doc_title', $edit_doc->description ?? '') }}" size="40" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Issue date:</td>
                    <td class="p-1"><input type="text" name="issue_date" value="{{ old('issue_date', isset($edit_doc) ? date('d/m/Y', strtotime($edit_doc->issue_date)) : date('d/m/Y')) }}" size="12" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Expiry date:</td>
                    <td class="p-1"><input type="text" name="expiry_date" value="{{ old('expiry_date', isset($edit_doc) ? date('d/m/Y', strtotime($edit_doc->expiry_date)) : '') }}" size="12" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Attached File:</td>
                    <td class="p-1"><input type="file" name="filename" class="border px-2 py-1"></td>
                </tr>
                <tr>
                    <td class="p-1 text-right font-semibold">Alert:</td>
                    <td class="p-1"><input type="checkbox" name="alert" value="1" {{ old('alert', $edit_doc->alert ?? false) ? 'checked' : '' }}></td>
                </tr>
            </table>
            <input type="hidden" name="selected_id" value="{{ $selected_id ?: '' }}">
            <div class="text-center mt-4">
                <input type="submit" name="process" value="{{ $selected_id && $selected_id != -1 ? 'Update' : 'Add' }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            </div>
        </div>
    @endif

    <input type="hidden" name="View" value="{{ $view_mode }}">
</form>
@endsection