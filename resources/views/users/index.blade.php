@extends('layouts.app')

@section('title', 'User & Role Management')
@section('page_title', 'User & Role Access Management')
@section('page_subtitle', 'Super Admin module: Manage system accounts, phone numbers and module permissions')

@section('content')
<div class="space-y-6">

    <!-- Role Summary Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-purple-200 dark:border-purple-900/50 shadow-sm flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Super Admins</p>
                <h3 class="text-2xl font-extrabold text-purple-600 dark:text-purple-400 font-display mt-1">{{ number_format($roleCounts['super_admin'] ?? 0) }}</h3>
            </div>
            <div class="p-3 bg-purple-100 dark:bg-purple-950/60 rounded-xl text-purple-600 dark:text-purple-400 font-bold">👑</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-indigo-200 dark:border-indigo-900/50 shadow-sm flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Admins</p>
                <h3 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 font-display mt-1">{{ number_format($roleCounts['admin'] ?? 0) }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 dark:bg-indigo-950/60 rounded-xl text-indigo-600 dark:text-indigo-400 font-bold">🛡️</div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-blue-200 dark:border-blue-900/50 shadow-sm flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Inventory Staff</p>
                <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 font-display mt-1">{{ number_format($roleCounts['staff'] ?? 0) }}</h3>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-950/60 rounded-xl text-blue-600 dark:text-blue-400 font-bold">📦</div>
        </div>

    </div>

    <!-- Header Action & Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 transition-colors">
        
        <form method="GET" action="{{ route('users.index') }}" class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search name, phone, email..." 
                   class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">

            <select name="role" class="py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>👑 Super Admin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>📦 Staff</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>🛍️ Customer</option>
            </select>

            <button type="submit" class="py-2 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all">
                Filter
            </button>
        </form>

        <a href="{{ route('users.create') }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-1.5 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create New User Account</span>
        </a>

    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[800px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 min-w-[160px]">User Details</th>
                        <th class="px-6 py-4 whitespace-nowrap">Phone Number (Login ID)</th>
                        <th class="px-6 py-4 whitespace-nowrap">Email</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Module Role</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Orders</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="ml-1 text-[9px] font-bold bg-amber-500/20 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded border border-amber-400/30">(You)</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 font-mono font-bold text-slate-800 dark:text-slate-200">
                                📞 {{ $user->phone }}
                            </td>

                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($user->isSuperAdmin())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        👑 Super Admin
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        🛡️ Admin
                                    </span>
                                @elseif($user->role === 'staff')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        📦 Staff
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        🛍️ Customer
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center font-bold text-slate-900 dark:text-white font-mono">
                                {{ $user->orders_count }}
                            </td>

                            <td class="px-6 py-4 text-right space-x-1.5">
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.impersonate', $user->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" title="Impersonate User Account" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-[11px] rounded-lg shadow-sm transition-all">
                                            🎭 Impersonate
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('users.edit', $user->id) }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px] rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                    Edit Role
                                </a>

                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user account?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold text-[11px] ml-1">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                No user accounts found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
