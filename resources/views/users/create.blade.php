@extends('layouts.app')

@section('title', 'Create User Account')
@section('page_title', 'Create System User Account')
@section('page_subtitle', 'Super Admin module: Register new user with phone login and module role assignment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 transition-colors">
        
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Babatunde Lawal"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (Login ID) <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 0803 123 4567"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="user@atlascollection.ng"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
            </div>

            <!-- Role Assignment -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Module Role & Permissions <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                        <input type="radio" name="role" value="super_admin" {{ old('role') === 'super_admin' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-purple-700 dark:text-purple-400"><i class="fa-solid fa-crown mr-1"></i> Super Admin</span>
                            <span class="block text-[10px] text-slate-500 dark:text-slate-400">Full system & user access</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                        <input type="radio" name="role" value="admin" {{ old('role', 'admin') === 'admin' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-indigo-700 dark:text-indigo-400"><i class="fa-solid fa-shield-halved mr-1"></i> Admin</span>
                            <span class="block text-[10px] text-slate-500 dark:text-slate-400">Orders, products & suppliers</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                        <input type="radio" name="role" value="staff" {{ old('role') === 'staff' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-blue-700 dark:text-blue-400"><i class="fa-solid fa-box-archive mr-1"></i> Staff</span>
                            <span class="block text-[10px] text-slate-500 dark:text-slate-400">Log stock & view orders</span>
                        </div>
                    </label>

                    <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                        <input type="radio" name="role" value="customer" {{ old('role') === 'customer' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-emerald-700 dark:text-emerald-400"><i class="fa-solid fa-bag-shopping mr-1"></i> Customer</span>
                            <span class="block text-[10px] text-slate-500 dark:text-slate-400">Storefront customer account</span>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required placeholder="Minimum 6 characters"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all">
                    Create User Account
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
