@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers')
@section('page_subtitle', 'View registered customer accounts, purchase histories, and total spend')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Registered Customers</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white font-display mt-0.5">{{ number_format($totalCustomersCount) }}</h3>
            </div>
            <div class="p-3 bg-amber-100 dark:bg-amber-950 text-amber-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('customers.index') }}" class="w-full sm:w-auto flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="🔍 Search customer name, phone, email..." 
                   class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">

            <select name="sort" @change="$el.closest('form').submit()" class="py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>✨ Newest Registered</option>
                <option value="ltv_desc" {{ request('sort') === 'ltv_desc' ? 'selected' : '' }}>👑 Top Spending (LTV)</option>
                <option value="orders_desc" {{ request('sort') === 'orders_desc' ? 'selected' : '' }}>📦 Most Orders Placed</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-800 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition-all">
                Filter
            </button>
            @if(request()->hasAny(['search', 'sort']))
                <a href="{{ route('customers.index') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">Reset</a>
            @endif
        </form>

        <div class="flex items-center space-x-2">
            <a href="{{ route('export.customers', request()->query()) }}" 
               title="Export customer directory and lifetime values to CSV"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow transition-all flex items-center space-x-1">
                <span>📥 Export CSV</span>
            </a>
            <a href="{{ route('orders.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow transition-all flex items-center space-x-1">
                <span>+ Create Customer Sale</span>
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[800px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 min-w-[160px]">Customer Name</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">VIP Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Contact Phone</th>
                        <th class="px-6 py-4 whitespace-nowrap">Email Address</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Total Orders</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Lifetime Value (LTV)</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Registered Date</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($customers as $cust)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('customers.show', $cust->phone ?? $cust->id) }}" class="hover:underline flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-700 dark:text-amber-400 font-extrabold flex items-center justify-center text-[10px]">
                                        {{ strtoupper(substr($cust->name, 0, 2)) }}
                                    </div>
                                    <span>{{ $cust->name }}</span>
                                </a>
                            </td>

                            <!-- VIP Badge -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] {{ $cust->vip_badge['class'] }}">
                                    {{ $cust->vip_badge['badge'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-mono font-semibold text-slate-800 dark:text-slate-200">
                                📞 {{ $cust->phone ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                {{ $cust->email }}
                            </td>

                            <td class="px-6 py-4 text-center font-bold text-slate-900 dark:text-white">
                                {{ $cust->orders_count }} order(s)
                            </td>

                            <td class="px-6 py-4 text-right font-mono font-black text-emerald-600 dark:text-emerald-400">
                                ₦{{ number_format($cust->total_spent ?? 0, 2) }}
                            </td>

                            <td class="px-6 py-4 text-center text-slate-400 text-[11px]">
                                {{ $cust->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('customers.show', $cust->phone ?? $cust->id) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-[11px] rounded-lg transition-all shadow-sm">
                                    View History &rarr;
                                </a>
                                <a href="{{ route('customers.edit', $cust->phone ?? $cust->id) }}" class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px] rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                    Edit
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('customers.destroy', $cust->phone ?? $cust->id) }}" onsubmit="return confirm('Delete customer account for {{ $cust->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 text-rose-600 hover:text-rose-700 font-semibold text-[11px]">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No registered customer records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
