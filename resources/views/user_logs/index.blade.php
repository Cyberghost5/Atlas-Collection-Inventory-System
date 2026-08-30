@extends('layouts.app')

@section('title', 'User Activity Logs')
@section('page_title', 'Staff Activity Logs')
@section('page_subtitle', 'Audit trail of staff logins, order processing, inventory changes, and system activities')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
        <form method="GET" action="{{ route('user-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Search Logs</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search action description, IP address, staff name..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Staff / User Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Staff / User</label>
                <select name="user_id" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Staff & Admins</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Action Type</label>
                <select name="action" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Actions</option>
                    @foreach($actionTypes as $aKey => $aLabel)
                        <option value="{{ $aKey }}" {{ request('action') === $aKey ? 'selected' : '' }}>
                            {{ $aLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Rows Per Page -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Rows Per Page</label>
                <select name="per_page" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 Logs</option>
                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 Logs</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Logs</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Logs</option>
                    <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 Logs</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl transition-all shadow-sm">
                    Filter Logs
                </button>
                @if(request()->hasAny(['search', 'user_id', 'action']))
                    <a href="{{ route('user-logs.index') }}" class="py-2 px-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- User Logs Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[900px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Timestamp</th>
                        <th class="px-6 py-4 whitespace-nowrap">User / Staff Member</th>
                        <th class="px-6 py-4 whitespace-nowrap">Action Type</th>
                        <th class="px-6 py-4 min-w-[280px]">Activity Description</th>
                        <th class="px-6 py-4 whitespace-nowrap">IP & Device Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            
                            <!-- Timestamp -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 dark:text-white block">
                                    {{ $log->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ $log->created_at->format('h:i:s A') }} ({{ $log->created_at->diffForHumans() }})
                                </span>
                            </td>

                            <!-- User Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $log->user->name }}
                                    </div>
                                    <div class="flex items-center space-x-2 mt-0.5">
                                        <span class="text-[10px] font-mono text-slate-400">{{ $log->user->phone }}</span>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider
                                            @if($log->user->role === 'super_admin') bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20
                                            @elseif($log->user->role === 'admin') bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20
                                            @else bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 @endif">
                                            {{ str_replace('_', ' ', $log->user->role) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">System / Anonymous</span>
                                @endif
                            </td>

                            <!-- Action Type Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($log->action)
                                    @case('login')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                            <i class="fa-solid fa-right-to-bracket text-emerald-500"></i>
                                            <span>User Login</span>
                                        </span>
                                        @break
                                    @case('logout')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/30">
                                            <i class="fa-solid fa-right-from-bracket text-slate-500"></i>
                                            <span>User Logout</span>
                                        </span>
                                        @break
                                    @case('order_created')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                            <i class="fa-solid fa-cash-register text-amber-500"></i>
                                            <span>Order Created</span>
                                        </span>
                                        @break
                                    @case('product_created')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/30">
                                            <i class="fa-solid fa-box-open text-blue-500"></i>
                                            <span>Product Added</span>
                                        </span>
                                        @break
                                    @case('product_updated')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30">
                                            <i class="fa-solid fa-pen-to-square text-indigo-500"></i>
                                            <span>Product Updated</span>
                                        </span>
                                        @break
                                    @case('product_deleted')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                                            <i class="fa-solid fa-trash-can text-rose-500"></i>
                                            <span>Product Deleted</span>
                                        </span>
                                        @break
                                    @case('stock_adjusted')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/30">
                                            <i class="fa-solid fa-boxes-packing text-purple-500"></i>
                                            <span>Stock Adjusted</span>
                                        </span>
                                        @break
                                    @case('cache_cleared')
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                                            <i class="fa-solid fa-broom text-amber-500"></i>
                                            <span>Cache Cleared</span>
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/30">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                            <span>{{ str_replace('_', ' ', ucfirst($log->action)) }}</span>
                                        </span>
                                @endswitch
                            </td>

                            <!-- Description -->
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900 dark:text-white block leading-relaxed">
                                    {{ $log->description }}
                                </span>
                            </td>

                            <!-- IP & Device Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300 block">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                                <span class="text-[10px] text-slate-400 truncate block max-w-[200px]" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?? 'Unknown Device', 30) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <p class="font-bold text-sm">No activity logs recorded yet.</p>
                                    <p class="text-xs text-slate-400">Staff logins, order sales, stock adjustments, and product edits will appear here automatically.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
