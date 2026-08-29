@extends('layouts.app')

@section('title', 'Edit User ' . $user->name)
@section('page_title', 'Edit User Account & Role')
@section('page_subtitle', 'Super Admin module: Update phone login, email, and assign module role permissions')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 transition-colors">
        
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (Login ID) <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
            </div>

            <!-- Role Assignment -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Module Role & Permissions <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer">
                        <input type="radio" name="role" value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-purple-700 dark:text-purple-400">👑 Super Admin</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer">
                        <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-indigo-700 dark:text-indigo-400">🛡️ Admin</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer">
                        <input type="radio" name="role" value="staff" {{ old('role', $user->role) === 'staff' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-blue-700 dark:text-blue-400">📦 Staff</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer">
                        <input type="radio" name="role" value="customer" {{ old('role', $user->role) === 'customer' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-emerald-700 dark:text-emerald-400">🛍️ Customer</span>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Optional New Password -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">New Password (Leave blank to keep unchanged)</label>
                <input type="password" name="password" placeholder="••••••••"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all">
                    Update User Account
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
