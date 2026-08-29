@extends('layouts.app')

@section('title', 'Payment Transactions Ledger')
@section('page_title', 'Transactions Ledger')
@section('page_subtitle', 'Audit and track all store sales payments, methods, and staff transaction logs')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Transactions</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white font-display mt-0.5">{{ number_format($totalTransactionsCount) }}</h3>
            </div>
            <div class="p-3 bg-amber-100 dark:bg-amber-950 text-amber-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Payment Volume (Paid)</p>
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">₦{{ number_format($totalVolumeAmount, 2) }}</h3>
            </div>
            <div class="p-3 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Today's Payment Volume</p>
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-0.5">₦{{ number_format($todayVolumeAmount, 2) }}</h3>
            </div>
            <div class="p-3 bg-indigo-100 dark:bg-indigo-950 text-indigo-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Search Payment / Staff / Customer</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Payment number, staff name, customer phone..." 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Payment Methods</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>💳 Bank Transfer</option>
                    <option value="pos" {{ request('payment_method') === 'pos' ? 'selected' : '' }}>📱 POS / Card</option>
                    <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>🌐 Other</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Payment Status</label>
                <select name="payment_status" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2 px-4 bg-slate-900 dark:bg-slate-800 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-all">
                    Filter Transactions
                </button>
                <a href="{{ route('export.transactions', request()->query()) }}" 
                   title="Export payment transactions ledger to CSV"
                   class="py-2 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1 flex-shrink-0">
                    <span>📥 Export CSV</span>
                </a>
                @if(request()->hasAny(['search', 'payment_method', 'payment_status']))
                    <a href="{{ route('transactions.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[850px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Payment Number</th>
                        <th class="px-6 py-4 whitespace-nowrap">Staff Logger</th>
                        <th class="px-6 py-4 min-w-[180px]">Customer</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Order Reference</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Amount (₦)</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Payment Method</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Date & Time</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            
                            <!-- Payment Number -->
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                <a href="{{ route('transactions.show', $trx->transaction_number) }}" class="hover:underline">
                                    #{{ $trx->transaction_number }}
                                </a>
                            </td>

                            <!-- Staff -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $trx->staff->name ?? 'System / Storefront' }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ strtoupper($trx->staff->role ?? 'Customer') }}
                                </div>
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $trx->customer_name }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">📞 {{ $trx->customer_phone ?? 'N/A' }}</span>
                            </td>

                            <!-- Reference Order Link -->
                            <td class="px-6 py-4 text-center font-mono font-bold">
                                @if($trx->order)
                                    <a href="{{ route('orders.show', $trx->order->order_number) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        #{{ $trx->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4 text-right font-mono font-black text-slate-900 dark:text-white text-sm">
                                ₦{{ number_format($trx->amount, 2) }}
                            </td>

                            <!-- Payment Method -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $trx->payment_method_badge }}">
                                    @if($trx->payment_method === 'cash') 💵 Cash
                                    @elseif($trx->payment_method === 'bank_transfer') 💳 Bank Transfer
                                    @elseif($trx->payment_method === 'pos') 📱 POS / Card
                                    @else 🌐 Other @endif
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider {{ $trx->payment_status_badge }}">
                                    {{ strtoupper($trx->payment_status) }}
                                </span>
                            </td>

                            <!-- Date & Time -->
                            <td class="px-6 py-4 text-center text-[11px] text-slate-400">
                                {{ $trx->created_at->format('M d, Y - h:i A') }}
                            </td>

                            <!-- Action Eye Icon -->
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('transactions.show', $trx->transaction_number) }}" 
                                   title="View Detailed Payment Info" 
                                   class="p-2 inline-flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400 hover:bg-amber-500 hover:text-slate-950 dark:hover:bg-amber-500 dark:hover:text-slate-950 rounded-xl transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                No payment transaction records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
