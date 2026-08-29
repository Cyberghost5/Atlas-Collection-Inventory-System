@extends('layouts.app')

@section('title', 'Customer VIP Profile - ' . $customer->name)
@section('page_title', 'Customer VIP Profile & Analytics')
@section('page_subtitle', 'Lifetime purchasing history, Average Order Value (AOV) & favorite catalog categories')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('customers.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center space-x-1">
            <span>&larr; Back to Customers Directory</span>
        </a>
    </div>

    <!-- Customer VIP Profile Summary Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6 transition-colors">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-xl shadow-lg font-display">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white font-display">{{ $customer->name }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs {{ $customer->vip_badge['class'] }}">
                            {{ $customer->vip_badge['badge'] }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Registered Customer • Member since {{ $customer->created_at->format('F Y') }}</p>
                </div>
            </div>

            <!-- WhatsApp Direct VIP Chat Button -->
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone ?? '');
                if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '234' . substr($cleanPhone, 1);
                }
                $waMsg = rawurlencode("Hello *{$customer->name}*!\nThank you for being a valued *{$customer->vip_badge['tier']}* customer at *Atlas Collection* (Bauchi Store).\n\nWe appreciate your continued patronage!");
                $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text={$waMsg}" : "https://api.whatsapp.com/send?text={$waMsg}";
            @endphp

            <div class="flex items-center space-x-2">
                <a href="{{ route('customers.edit', $customer->phone ?? $customer->id) }}" class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow transition-all flex items-center space-x-1.5">
                    <span>✏️ Edit Profile</span>
                </a>
                <a href="{{ $waUrl }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs rounded-xl shadow transition-all flex items-center space-x-1.5">
                    <span>📱 Chat with VIP Customer</span>
                </a>
            </div>
        </div>

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                <span class="block text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Lifetime Value (LTV)</span>
                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1 block">₦{{ number_format($customer->ltv, 2) }}</span>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                <span class="block text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Average Order Value (AOV)</span>
                <span class="text-xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-1 block">₦{{ number_format($customer->average_order_value, 2) }}</span>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                <span class="block text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Completed Orders</span>
                <span class="text-xl font-black text-slate-900 dark:text-white font-display mt-1 block">{{ $customer->orders_count }} order(s)</span>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                <span class="block text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Top Purchased Category</span>
                <span class="text-sm font-black text-amber-600 dark:text-amber-400 font-display mt-1 block truncate" title="{{ $favoriteCategory }}">🛍️ {{ $favoriteCategory }}</span>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Phone Number</span>
                <span class="font-mono font-bold text-slate-900 dark:text-white">📞 {{ $customer->phone ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Email Address</span>
                <span class="text-slate-700 dark:text-slate-300">✉️ {{ $customer->email ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Shipping Address</span>
                <span class="text-slate-700 dark:text-slate-300">📍 {{ $customer->address ?? 'Store Pickup - Bauchi Main Store' }}</span>
            </div>
        </div>
    </div>

    <!-- Orders History Timeline -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden space-y-3 p-6 transition-colors">
        <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
            Order Purchase Timeline
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[800px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-semibold text-[10px]">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Order Ref</th>
                        <th class="px-4 py-3 whitespace-nowrap">Date</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Items Count</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Amount (₦)</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($customer->orders as $order)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                #{{ $order->order_number }}
                            </td>
                            <td class="px-4 py-3 text-slate-400 whitespace-nowrap">
                                {{ $order->created_at->format('M d, Y - h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold whitespace-nowrap">
                                {{ $order->orderItems->count() }} item(s)
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                ₦{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $order->status_badge }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('orders.show', $order->order_number) }}" class="px-3 py-1 bg-slate-900 dark:bg-slate-800 text-white rounded-lg text-[11px] font-bold hover:bg-slate-800 transition-all">
                                    View Order &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                No purchase order records logged for this customer yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
