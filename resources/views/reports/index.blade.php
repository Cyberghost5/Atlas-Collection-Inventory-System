@extends('layouts.app')

@section('title', 'Executive Reports & Visual Analytics')
@section('page_title', 'Executive Reports & Analytics')
@section('page_subtitle', 'Comprehensive business performance analytics, financial metrics, and revenue trends')

@section('content')
<div class="space-y-6" x-data>

    <!-- Top Header & Date Timeframe Filter -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Performance Report Period</h3>
            <p class="text-xs text-slate-400">Select a timeframe to filter all metrics, charts, and financial data</p>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center space-x-3 w-full sm:w-auto">
            <label class="text-xs font-bold text-slate-600 dark:text-slate-400 whitespace-nowrap">Timeframe:</label>
            <select name="timeframe" onchange="this.form.submit()" class="w-full sm:w-56 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                <option value="all_time" {{ $timeframe === 'all_time' ? 'selected' : '' }}>All Time (Full History)</option>
                <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $timeframe === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="last_7_days" {{ $timeframe === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="last_30_days" {{ $timeframe === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_month" {{ $timeframe === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $timeframe === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $timeframe === 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Total Sales Revenue -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Total Sales Revenue</span>
            <div class="text-xl font-black text-slate-900 dark:text-white font-mono">₦{{ number_format($totalRevenue, 2) }}</div>
            <p class="text-[10px] text-slate-400">Total gross sales value</p>
        </div>

        <!-- Cost of Goods Sold (Admin Confidential) -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Cost of Goods Sold</span>
            @if(auth()->user()->isAdmin())
                <div class="text-xl font-black text-rose-600 dark:text-rose-400 font-mono">₦{{ number_format($totalCostOfGoods, 2) }}</div>
                <p class="text-[10px] text-slate-400">Total item cost price</p>
            @else
                <div class="text-xs font-bold text-slate-400 mt-2"><i class="fa-solid fa-lock text-slate-400 mr-1"></i> Admin Confidential</div>
            @endif
        </div>

        <!-- Net Profit (Admin Confidential) -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Net Gross Profit</span>
            @if(auth()->user()->isAdmin())
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">₦{{ number_format($netProfit, 2) }}</div>
                <p class="text-[10px] text-emerald-500 font-bold">Revenue &minus; Item Costs</p>
            @else
                <div class="text-xs font-bold text-slate-400 mt-2"><i class="fa-solid fa-lock text-slate-400 mr-1"></i> Admin Confidential</div>
            @endif
        </div>

        <!-- Total Orders -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Total Orders Logged</span>
            <div class="text-xl font-black text-amber-500 font-display">{{ number_format($totalOrdersCount) }}</div>
            <p class="text-[10px] text-slate-400">Completed & active orders</p>
        </div>

        <!-- Avg Order Value -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-1">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Average Order Value</span>
            <div class="text-xl font-black text-indigo-600 dark:text-indigo-400 font-mono">₦{{ number_format($avgOrderValue, 2) }}</div>
            <p class="text-[10px] text-slate-400">Avg value per sale</p>
        </div>

    </div>

    <!-- Visual Analytics Grid: Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Chart 1: Revenue Trend Over Time -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Revenue Trend Over Time</h4>
                    <p class="text-[11px] text-slate-400">Sales volume performance across selected period</p>
                </div>
                <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 rounded-lg text-[10px] font-black uppercase">Bar Chart</span>
            </div>
            <div class="relative h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Payment Methods Distribution -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Payment Methods Share</h4>
                    <p class="text-[11px] text-slate-400">Incoming revenue distribution by payment channel</p>
                </div>
                <span class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 rounded-lg text-[10px] font-black uppercase">Pie Chart</span>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>

        <!-- Chart 3: Top Selling Apparel Products -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 {{ auth()->user()->isAdmin() ? '' : 'lg:col-span-2' }}">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Top Selling Products</h4>
                    <p class="text-[11px] text-slate-400">Highest grossing items by total sales revenue</p>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 rounded-lg text-[10px] font-black uppercase">Bar Chart</span>
            </div>
            <div class="relative h-64">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
            <!-- Chart 4: Revenue vs Net Profit (Admin Confidential) -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">4. Profit vs Cost Share</h4>
                        <p class="text-[11px] text-slate-400">Net Profit vs Cost of Goods Sold ratio</p>
                    </div>
                    <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 rounded-lg text-[10px] font-black uppercase">Doughnut Chart</span>
                </div>
                <div class="relative h-64 flex items-center justify-center">
                    <canvas id="profitChart"></canvas>
                </div>
            </div>
        @endif

    </div>

    <!-- Section 5: Revenue Table by Payment Method -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
        <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
            5. Incoming Revenue Summary by Payment Method
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Payment Method</th>
                        <th class="px-4 py-3 text-center">Transactions Count</th>
                        <th class="px-4 py-3 text-right">Total Amount (₦)</th>
                        <th class="px-4 py-3 text-right">Revenue Share (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($pmTable as $row)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                {{ $row['name'] }}
                            </td>
                            <td class="px-4 py-3 text-center font-semibold">
                                {{ number_format($row['count']) }} txns
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-white">
                                ₦{{ number_format($row['amount'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $row['percentage'] }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 6: Top Performers Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Top 10 Products by Revenue -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                6A. Top 10 Apparel Items by Revenue
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-semibold text-[10px]">
                        <tr>
                            <th class="px-3 py-2.5">Apparel Item</th>
                            <th class="px-3 py-2.5 text-center">Units Sold</th>
                            <th class="px-3 py-2.5 text-right">Revenue (₦)</th>
                            @if(auth()->user()->isAdmin())
                                <th class="px-3 py-2.5 text-right">Net Profit (₦)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($topProductTable as $tp)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">
                                    {{ $tp['name'] }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $tp['category'] }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold">{{ $tp['units_sold'] }}</td>
                                <td class="px-3 py-2.5 text-right font-mono font-bold text-slate-900 dark:text-white">₦{{ number_format($tp['revenue'], 2) }}</td>
                                @if(auth()->user()->isAdmin())
                                    <td class="px-3 py-2.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">₦{{ number_format($tp['profit'], 2) }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ auth()->user()->isAdmin() ? 4 : 3 }}" class="py-6 text-center text-slate-400">No product sales logged in period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top 10 Customers by Order Value -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                6B. Top 10 Customers by Order Value
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-semibold text-[10px]">
                        <tr>
                            <th class="px-3 py-2.5">Customer</th>
                            <th class="px-3 py-2.5 text-center">Orders Count</th>
                            <th class="px-3 py-2.5 text-right">Total Spent (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($topCustomers as $tc)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                                <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-white">
                                    {{ $tc->customer_name }}
                                    <span class="block text-[10px] text-slate-400 font-mono"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $tc->customer_phone ?? 'N/A' }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold">{{ $tc->total_orders }} order(s)</td>
                                <td class="px-3 py-2.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">₦{{ number_format($tc->total_spent, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-slate-400">No customer records logged in period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Section 7: Recent Orders Table (Last 20 in Period) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
        <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
            7. Recent Orders Ledger (Last 20 in Period)
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[750px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-semibold text-[10px]">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Order Ref</th>
                        <th class="px-4 py-3 min-w-[160px]">Customer</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Items</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Amount (₦)</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Payment Method</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentOrders as $ro)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50">
                            <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                <a href="{{ route('orders.show', $ro->order_number) }}" class="hover:underline">
                                    #{{ $ro->order_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $ro->customer_name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $ro->customer_phone }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold">{{ $ro->orderItems->count() }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-white">₦{{ number_format($ro->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-center uppercase font-bold text-[10px]">
                                {{ str_replace('_', ' ', $ro->payment_method ?? 'cash') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $ro->status_badge }}">
                                    {{ strtoupper($ro->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-slate-400 text-[11px]">{{ $ro->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-400">No recent orders in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#475569';
    const gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)';

    // Chart 1: Revenue Trend Over Time
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($trendData)) !!},
            datasets: [{
                label: 'Revenue (₦)',
                data: {!! json_encode(array_values($trendData)) !!},
                backgroundColor: 'rgba(245, 158, 11, 0.85)',
                borderColor: '#f59e0b',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: textColor }, grid: { display: false } },
                y: { ticks: { color: textColor }, grid: { color: gridColor } }
            }
        }
    });

    // Chart 2: Payment Methods Share (Pie Chart)
    const pmCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(pmCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($pmLabels) !!},
            datasets: [{
                data: {!! json_encode($pmAmounts) !!},
                backgroundColor: ['#10b981', '#6366f1', '#a855f7', '#64748b'],
                borderWidth: 2,
                borderColor: isDark ? '#0f172a' : '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { size: 11, weight: 'bold' } } }
            }
        }
    });

    // Chart 3: Top Selling Apparel Products
    const topProdCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProdCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProductLabels) !!},
            datasets: [{
                label: 'Sales Revenue (₦)',
                data: {!! json_encode($topProductRevenues) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: textColor }, grid: { color: gridColor } },
                y: { ticks: { color: textColor }, grid: { display: false } }
            }
        }
    });

    @if(auth()->user()->isAdmin())
        // Chart 4: Profit vs Cost Share (Admin Confidential Doughnut Chart)
        const profitCanvas = document.getElementById('profitChart');
        if (profitCanvas) {
            const profitCtx = profitCanvas.getContext('2d');
            new Chart(profitCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Net Gross Profit (₦)', 'Cost of Goods Sold (₦)'],
                    datasets: [{
                        data: [{{ max(0, $netProfit) }}, {{ $totalCostOfGoods }}],
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderWidth: 2,
                        borderColor: isDark ? '#0f172a' : '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: textColor, font: { size: 11, weight: 'bold' } } }
                    }
                }
            });
        }
    @endif
});
</script>
@endsection
