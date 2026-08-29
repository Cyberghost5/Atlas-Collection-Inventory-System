@extends('layouts.app')

@section('title', 'Orders')
@section('page_title', 'Orders & Delivery Management')
@section('page_subtitle', 'Process e-commerce orders, update statuses & track nationwide Nigerian logistics')

@section('content')
<div class="space-y-6">

    <!-- KPI & Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="block group">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-amber-200 dark:border-amber-900/50 shadow-sm flex items-center justify-between group-hover:shadow-md transition-all">
                <div>
                    <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pending Orders</p>
                    <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 font-display mt-1">{{ number_format($pendingCount) }}</h3>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-950/60 rounded-xl text-amber-600 dark:text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="block group">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-blue-200 dark:border-blue-900/50 shadow-sm flex items-center justify-between group-hover:shadow-md transition-all">
                <div>
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Processing / Packaging</p>
                    <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 font-display mt-1">{{ number_format($processingCount) }}</h3>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-950/60 rounded-xl text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8zm14 0l-2-4H7L5 8"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="block group">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-emerald-200 dark:border-emerald-900/50 shadow-sm flex items-center justify-between group-hover:shadow-md transition-all">
                <div>
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Completed / Delivered</p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 font-display mt-1">{{ number_format($completedCount) }}</h3>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-950/60 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="block group">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between group-hover:shadow-md transition-all">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cancelled Orders</p>
                    <h3 class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 font-display mt-1">{{ number_format($cancelledCount) }}</h3>
                </div>
                <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-500 dark:text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </a>

    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Search Customer / Order</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Order number, customer name, phone..." 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Order Status</label>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Order Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-2 flex items-end space-x-2">
                <button type="submit" class="w-full py-2 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-sm transition-all">
                    Filter Orders
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('orders.index') }}" class="py-2 px-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                        Reset
                    </a>
                @endif
                <a href="{{ route('export.orders', request()->query()) }}" 
                   title="Export filtered orders ledger to CSV"
                   class="py-2 px-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-1 flex-shrink-0">
                    <span><i class="fa-solid fa-file-csv mr-1"></i> Export CSV</span>
                </a>
                <a href="{{ route('orders.create') }}" class="py-2 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow transition-all flex items-center justify-center space-x-1 flex-shrink-0">
                    <span>+ Record Sale</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[850px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Order Number</th>
                        <th class="px-6 py-4 min-w-[180px]">Customer</th>
                        <th class="px-6 py-4 min-w-[200px]">Delivery Location</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Items</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Total Amount</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 dark:text-amber-400 whitespace-nowrap">
                                <a href="{{ route('orders.show', $order) }}" class="hover:underline">
                                    #{{ $order->order_number }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-normal font-sans">{{ $order->created_at->format('M d, Y - h:i A') }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $order->customer_name }}</span>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono whitespace-nowrap"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $order->customer_phone }}</div>
                                <div class="text-[10px] text-slate-400 whitespace-nowrap"><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $order->customer_email }}</div>
                            </td>

                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                {{ $order->shipping_address }}
                            </td>

                            <td class="px-6 py-4 text-center font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                {{ $order->orderItems->count() }} item(s)
                            </td>

                            <td class="px-6 py-4 text-right font-black text-slate-900 dark:text-white font-mono whitespace-nowrap">
                                ₦{{ number_format($order->total_amount, 2) }}
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $order->status_badge }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('orders.show', $order) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-[11px] rounded-lg transition-all shadow-sm">
                                    Process Order &rarr;
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this order? Stock will be restored to inventory.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold text-[11px]">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                No orders found matching filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
