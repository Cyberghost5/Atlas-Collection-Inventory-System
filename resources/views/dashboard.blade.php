@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Executive Dashboard' : 'Staff Sales & Operations Dashboard')
@section('page_title', auth()->user()->isAdmin() ? 'Executive Dashboard' : 'Staff Sales & Operations')
@section('page_subtitle', auth()->user()->isAdmin() ? 'Key performance financial metrics & stock overview' : 'Record daily counter sales, process orders & track inventory')

@section('content')
<div class="space-y-6">

    @if(auth()->user()->isAdmin())
        <!-- ================================================================= -->
        <!-- 👑 ADMIN & SUPER ADMIN EXECUTIVE DASHBOARD VIEW                  -->
        <!-- ================================================================= -->

        <!-- Header Actions & Date Range Filter Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white font-display">Executive Overview</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 uppercase tracking-wider">
                        {{ $timeframeLabels[$timeframe] ?? 'All Time' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atlas Collection • Bauchi Store</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Date Timeframe Dropdown -->
                <form method="GET" action="{{ route('dashboard') }}" class="inline-block">
                    <select name="timeframe" 
                            onchange="this.form.submit()" 
                            class="py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500 focus:bg-white dark:focus:bg-slate-900 transition-all cursor-pointer">
                        <option value="all_time" {{ $timeframe === 'all_time' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $timeframe === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="last_7_days" {{ $timeframe === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last_30_days" {{ $timeframe === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_month" {{ $timeframe === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $timeframe === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    </select>
                </form>

                <form method="POST" action="{{ route('admin.clear-cache') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-800 dark:text-slate-200 hover:text-rose-600 font-bold text-xs rounded-xl transition-all border border-slate-200 dark:border-slate-700 flex items-center space-x-1.5" title="Clear Application & Cache Files">
                        <i class="fa-solid fa-broom text-amber-500"></i>
                        <span>Clear Cache</span>
                    </button>
                </form>

                <a href="{{ route('orders.create') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                    <span>+ Record Sale</span>
                </a>
                <a href="{{ route('products.create') }}" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                    <span>+ Add Item</span>
                </a>
                <a href="{{ route('orders.index') }}" class="px-3.5 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition-all">
                    Orders ({{ $pendingOrdersCount }})
                </a>
            </div>
        </div>

        <!-- ADMIN PRIMARY FINANCIAL METRICS GRID -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Sales Revenue -->
            <a href="{{ route('reports.index') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Sales</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-display mt-1">
                        ₦{{ number_format($totalSalesRevenue, 2) }}
                    </div>
                    <span class="text-[11px] text-emerald-600 font-semibold block mt-1">
                        {{ $timeframeLabels[$timeframe] ?? 'All Time' }} Revenue
                    </span>
                </div>
            </a>

            <!-- Total Orders -->
            <a href="{{ route('orders.index') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm group-hover:border-amber-400 transition-all">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Orders</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-display mt-1">
                        {{ number_format($totalOrdersCount) }}
                    </div>
                    <span class="text-[11px] text-amber-600 font-semibold block mt-1">{{ $pendingOrdersCount }} Pending</span>
                </div>
            </a>

            <!-- Stock Valuation -->
            <a href="{{ route('reports.index') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Stock Cost Value</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-display mt-1">
                        ₦{{ number_format($totalCostValue, 2) }}
                    </div>
                    <span class="text-[11px] text-slate-400 block mt-1">Total Cost Valuation</span>
                </div>
            </a>

            <!-- Low Stock -->
            <a href="{{ route('products.low-stock') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border {{ $lowStockCount > 0 ? 'border-rose-200 dark:border-rose-900 bg-rose-50/30 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-800' }} shadow-sm group-hover:border-rose-400 transition-all">
                    <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Low Stock</span>
                    <div class="text-2xl sm:text-3xl font-black text-rose-600 font-display mt-1">
                        {{ number_format($lowStockCount) }}
                    </div>
                    <span class="text-[11px] text-rose-500 font-semibold block mt-1">Below 10 units</span>
                </div>
            </a>

        </div>

        <!-- SECONDARY METRICS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-center transition-colors">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Active Catalog Items</span>
                <div class="text-xl font-black text-slate-900 dark:text-white font-display mt-0.5">{{ number_format($totalProducts) }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-center transition-colors">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Active Staff</span>
                <div class="text-xl font-black text-slate-900 dark:text-white font-display mt-0.5">{{ number_format($totalStaffCount) }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-center transition-colors">
                <span class="text-[10px] font-bold text-emerald-600 uppercase">Restocked Units</span>
                <div class="text-xl font-black text-emerald-600 font-display mt-0.5">{{ number_format($movementCounts['in'] ?? 0) }}</div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-center transition-colors">
                <span class="text-[10px] font-bold text-blue-600 uppercase">Sold Units</span>
                <div class="text-xl font-black text-blue-600 font-display mt-0.5">{{ number_format($movementCounts['out_sale'] ?? 0) }}</div>
            </div>
        </div>

        <!-- LOW STOCK & RECENT MOVEMENTS LISTS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Low Stock Items -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Low Stock (&lt; 10 Units)</h3>
                    <a href="{{ route('products.low-stock') }}" class="text-xs text-amber-600 hover:underline font-bold">View All &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($lowStockProducts as $item)
                        <div class="p-3.5 flex items-center justify-between">
                            <div>
                                <a href="{{ route('products.show', $item) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-500">
                                    {{ $item->name }}
                                </a>
                                <span class="text-[10px] text-slate-400 block">Size: {{ $item->size }} • SKU: {{ $item->sku }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold rounded text-xs">
                                    {{ $item->stock_quantity }} {{ $item->unit }}
                                </span>
                                @if($item->supplier && $item->supplier->phone)
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $item->supplier->phone);
                                        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '234' . substr($cleanPhone, 1);
                                        }
                                        $supplierMsg = rawurlencode("Hello {$item->supplier->name}! Restock order: {$item->name} (SKU: {$item->sku}, Size: {$item->size}) - Current Stock: {$item->stock_quantity}.");
                                        $supplierWaUrl = "https://wa.me/{$cleanPhone}?text={$supplierMsg}";
                                    @endphp
                                    <a href="{{ $supplierWaUrl }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 text-white text-[11px] font-bold rounded hover:bg-emerald-700">
                                        Supplier
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            All items are adequately stocked (&ge; 10 units).
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity Ledger -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Recent Activity Ledger</h3>
                    <a href="{{ route('stock-movements.index') }}" class="text-xs text-amber-600 hover:underline font-bold">View Ledger &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($recentMovements as $mv)
                        <div class="p-3.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $mv->product->name ?? 'Deleted Item' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $mv->created_at->format('M d, h:i A') }} • {{ $mv->user->name ?? 'System' }}</span>
                            </div>
                            <div>
                                @if($mv->type === 'in')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">+{{ $mv->quantity }} Restock</span>
                                @elseif($mv->type === 'out_sale')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">-{{ $mv->quantity }} Sale</span>
                                @elseif($mv->type === 'out_internal')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">-{{ $mv->quantity }} Sample</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Adj: {{ $mv->quantity }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">No stock movements logged.</div>
                    @endforelse
                </div>
            </div>
        </div>

    @else
        <!-- ================================================================= -->
        <!-- 💼 STAFF SALES & OPERATIONAL COUNTER DASHBOARD VIEW              -->
        <!-- ================================================================= -->

        <!-- Quick Action Hero Banner for Staff -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-lg border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950 uppercase tracking-widest">
                    Staff Sales Portal
                </span>
                <h2 class="text-xl font-bold font-display mt-2">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-xs text-slate-300 mt-1">Log new walk-in customer sales, register new customers, and update order statuses.</p>
            </div>
            <div>
                <a href="{{ route('orders.create') }}" class="px-5 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-amber-500/20 transition-all inline-flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>+ Record Sale Now</span>
                </a>
            </div>
        </div>

        <!-- STAFF OPERATIONAL METRICS GRID (No Financial Cost/Revenue Figures) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Pending Customer Orders Needing Processing -->
            <a href="{{ route('orders.index') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm group-hover:border-amber-400 transition-all">
                    <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider block">Pending Orders</span>
                    <div class="text-2xl sm:text-3xl font-black text-amber-500 font-display mt-1">
                        {{ number_format($pendingOrdersCount) }}
                    </div>
                    <span class="text-[11px] text-slate-400 block mt-1">Needs Staff Processing</span>
                </div>
            </a>

            <!-- Total Completed Customer Orders -->
            <a href="{{ route('orders.index') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm group-hover:border-emerald-400 transition-all">
                    <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Completed Orders</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-600 font-display mt-1">
                        {{ number_format($completedOrdersCount) }}
                    </div>
                    <span class="text-[11px] text-slate-400 block mt-1">Fulfilling / Completed</span>
                </div>
            </a>

            <!-- Total Catalog Items -->
            <a href="{{ route('products.index') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm group-hover:border-indigo-400 transition-all">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Available Apparel</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-display mt-1">
                        {{ number_format($totalProducts) }}
                    </div>
                    <span class="text-[11px] text-indigo-500 font-semibold block mt-1">Storefront Catalog</span>
                </div>
            </a>

            <!-- Low Stock Items -->
            <a href="{{ route('products.low-stock') }}" class="block group">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border {{ $lowStockCount > 0 ? 'border-rose-200 dark:border-rose-900 bg-rose-50/30 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-800' }} shadow-sm group-hover:border-rose-400 transition-all">
                    <span class="text-xs font-semibold text-rose-600 uppercase tracking-wider block">Low Stock Items</span>
                    <div class="text-2xl sm:text-3xl font-black text-rose-600 font-display mt-1">
                        {{ number_format($lowStockCount) }}
                    </div>
                    <span class="text-[11px] text-rose-500 font-semibold block mt-1">Below 10 Units Alert</span>
                </div>
            </a>

        </div>

        <!-- STAFF OPERATIONAL TABLES: RECENT CUSTOMER ORDERS & LOW STOCK -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Recent Customer Orders Needing Processing -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Recent Customer Orders</h3>
                    <a href="{{ route('orders.index') }}" class="text-xs text-amber-600 hover:underline font-bold">View All Orders &rarr;</a>
                </div>

                @php
                    $staffRecentOrders = \App\Models\Order::latest()->take(6)->get();
                @endphp

                <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($staffRecentOrders as $ord)
                        <div class="p-3.5 flex items-center justify-between">
                            <div>
                                <a href="{{ route('orders.show', $ord->order_number) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline block">
                                    #{{ $ord->order_number }}
                                </a>
                                <span class="text-[10px] text-slate-400 block">{{ $ord->customer_name }} • <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $ord->customer_phone }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $ord->status_badge }}">
                                    {{ strtoupper($ord->status) }}
                                </span>
                                <a href="{{ route('orders.show', $ord->order_number) }}" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded text-[11px] hover:bg-slate-200">
                                    View
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">No recent orders found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Low Stock Inventory Items Alert -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Low Stock Inventory (&lt; 10 Units)</h3>
                    <a href="{{ route('products.low-stock') }}" class="text-xs text-rose-600 hover:underline font-bold">View Low Stock &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($lowStockProducts as $item)
                        <div class="p-3.5 flex items-center justify-between">
                            <div>
                                <a href="{{ route('products.show', $item) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-500">
                                    {{ $item->name }}
                                </a>
                                <span class="text-[10px] text-slate-400 block">Size: {{ $item->size }} • SKU: {{ $item->sku }}</span>
                            </div>
                            <div>
                                <span class="px-2 py-1 bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold rounded text-xs">
                                    {{ $item->stock_quantity }} {{ $item->unit }}(s) left
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">All apparel items adequately stocked (&ge; 10 units).</div>
                    @endforelse
                </div>
            </div>

        </div>

    @endif

</div>
@endsection
