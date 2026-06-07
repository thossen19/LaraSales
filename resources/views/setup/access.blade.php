@extends('layouts.app')
@section('title', 'Access Setup - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Access Setup</h2>
    <p class="mt-2 text-gray-600">Configure access controls and security roles.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.access') }}" class="bg-white shadow rounded-lg">
    @csrf

    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center gap-6 flex-wrap">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Role:</label>
                <select name="role" onchange="window.location='{{ route('setup.access') }}?role='+this.value" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- New Role --</option>
                    @foreach($allRoles ?? [] as $r)
                        <option value="{{ $r->id }}" {{ ($selectedRoleId ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center text-sm">
                <input type="checkbox" name="show_inactive" value="1" {{ request('show_inactive') ? 'checked' : '' }} onchange="window.location='{{ route('setup.access') }}?show_inactive='+(this.checked?1:0)+'&role='+('{{ $selectedRoleId ?? '' }}')" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-gray-700">Show inactive:</span>
            </label>
        </div>
    </div>

    <div class="p-6 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role name *</label>
                <input type="text" name="name" value="{{ old('name', $selectedRole->name ?? '') }}" maxlength="22" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role description</label>
                <input type="text" name="description" value="{{ old('description', $selectedRoleDesc ?? '') }}" maxlength="52" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current status</label>
                <select name="inactive" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0" {{ old('inactive', $selectedRoleInactive ?? '0') == '0' ? 'selected' : '' }}>Active</option>
                    <option value="1" {{ old('inactive', $selectedRoleInactive ?? '0') == '1' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Module Access Permissions</h3>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module / Permission</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Access</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php $rowColor = 0; @endphp
                @foreach($permissionGroups ?? [] as $sectionName => $perms)
                @php
                    $sectionKey = 'section_' . Str::slug($sectionName, '_');
                    $sectionChecked = $rolePermissions->contains(function($p) use ($perms) {
                        return in_array($p->name, $perms);
                    });
                @endphp
                <tr class="{{ $rowColor++ % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $sectionName }}:</td>
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox" name="{{ $sectionKey }}" value="1" {{ $sectionChecked ? 'checked' : '' }}
                               onchange="toggleSection(this, '{{ $sectionKey }}_area')"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 section-toggle">
                    </td>
                </tr>
                @foreach($perms as $perm)
                <tr class="{{ $rowColor++ % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-4 py-2 text-sm text-gray-700 pl-10">{{ $perm }}</td>
                    <td class="px-4 py-2 text-center">
                        <input type="checkbox" name="perm_{{ $perm }}" value="1"
                               {{ $rolePermissions->contains('name', $perm) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 {{ $sectionKey }}_area area-checkbox">
                    </td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 bg-gray-50 rounded-b-lg border-t border-gray-200 flex items-center gap-3 flex-wrap">
        @if(!$selectedRoleId)
            <button type="submit" name="action" value="insert" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">Insert New Role</button>
        @else
            <button type="submit" name="action" value="save" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">Save Role</button>
            <button type="submit" name="action" value="clone" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">Clone This Role</button>
            <button type="submit" name="action" value="delete" class="px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50 transition duration-150" onclick="return confirm('Delete this role?')">Delete This Role</button>
            <a href="{{ route('setup.access') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">Cancel</a>
        @endif
    </div>

    <input type="hidden" name="role_id" value="{{ $selectedRoleId ?? '' }}">
</form>

@push('scripts')
<script>
function toggleSection(checkbox, prefix) {
    document.querySelectorAll('.' + prefix).forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.section-toggle').forEach(function(sectionCb) {
        var prefix = sectionCb.getAttribute('onchange').match(/'([^']+)'/)[1];
        var allChecked = true;
        document.querySelectorAll('.' + prefix).forEach(function(areaCb) {
            if (!areaCb.checked) allChecked = false;
        });
        sectionCb.checked = allChecked;
    });
});
</script>
@endpush
@endsection