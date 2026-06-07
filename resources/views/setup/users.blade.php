@extends('layouts.app')
@section('title', 'User Accounts Setup - Sales ERP')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">User Accounts Setup</h2>
    <p class="mt-2 text-gray-600">Manage user accounts, access levels and preferences.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('setup.users') }}" class="bg-white shadow rounded-lg mb-8">
    @csrf
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User login</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access Level</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inactive</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users ?? [] as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $user->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">{{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i') : 'Never' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <label class="flex items-center">
                            <input type="checkbox" name="inactive[{{ $user->id }}]" value="1" {{ !$user->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </label>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('setup.users', ['edit' => $user->id]) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if(auth()->id() != $user->id)
                        <button type="submit" name="delete" value="{{ $user->id }}" class="text-red-600 hover:text-red-900 font-medium" onclick="return confirm('Delete this user?')">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-6 border-t border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editUser ? 'Edit User' : 'Add New User' }}</h3>
        <input type="hidden" name="selected_id" value="{{ optional($editUser)->id ?? '' }}">
        @if($editUser)
        <input type="hidden" name="user_id" value="{{ $editUser->email }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if(!$editUser)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User Login *</label>
                <input type="email" name="user_id" value="{{ old('user_id') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            @else
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User login</label>
                <p class="py-2 text-gray-900 font-medium">{{ $editUser->email }}</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password {{ $editUser ? '(leave empty to keep current)' : '*' }}
                </label>
                <input type="password" name="password" {{ $editUser ? '' : 'required' }} minlength="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @if($editUser)
                <p class="text-xs text-gray-500 mt-1">Enter a new password to change, leave empty to keep current.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="real_name" value="{{ old('real_name', optional($editUser)->name ?? '') }}" required maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telephone No.</label>
                <input type="text" name="phone" value="{{ old('phone', optional($editUser)->phone ?? '') }}" maxlength="30" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', optional($editUser)->email ?? '') }}" required maxlength="50" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Access Level</label>
                <select name="role_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Access Level</option>
                    @foreach($roles ?? [] as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', optional(optional($editUser)->roles)->first()->id ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                <select name="language" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="en_US" {{ old('language', optional($editUser)->language ?? 'en_US') == 'en_US' ? 'selected' : '' }}>English</option>
                    <option value="fr_FR" {{ old('language') == 'fr_FR' ? 'selected' : '' }}>French</option>
                    <option value="de_DE" {{ old('language') == 'de_DE' ? 'selected' : '' }}>German</option>
                    <option value="es_ES" {{ old('language') == 'es_ES' ? 'selected' : '' }}>Spanish</option>
                    <option value="ar_SA" {{ old('language') == 'ar_SA' ? 'selected' : '' }}>Arabic</option>
                    <option value="hi_IN" {{ old('language') == 'hi_IN' ? 'selected' : '' }}>Hindi</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User's POS</label>
                <select name="pos" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0" {{ old('pos', optional($editUser)->pos ?? '0') == '0' ? 'selected' : '' }}>Default</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Printing profile</label>
                <select name="print_profile" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0" {{ old('print_profile', optional($editUser)->print_profile ?? '0') == '0' ? 'selected' : '' }}>Browser printing support</option>
                    <option value="1" {{ old('print_profile') == '1' ? 'selected' : '' }}>PDF</option>
                </select>
            </div>

            <div class="flex items-center">
                <label class="flex items-center">
                    <input type="checkbox" name="rep_popup" value="1" {{ old('rep_popup', optional($editUser)->rep_popup ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Use popup window for reports</span>
                </label>
                <p class="ml-2 text-xs text-gray-500">Set this option to on if your browser directly supports pdf files</p>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" name="update" value="1" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                {{ $editUser ? 'Update' : 'Add New' }}
            </button>
            @if($editUser)
            <a href="{{ route('setup.users') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition duration-150">Cancel</a>
            @endif
        </div>
    </div>
</form>
@endsection