@extends('layouts.storefront')

@section('title', 'My Apparel Orders')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-black font-display text-white">My Apparel Order History</h1>
            <p class="text-xs text-slate-400">Track and manage your streetwear orders delivered across Nigeria</p>
        </div>
    </div>

    <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Order Number</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Items</th>
                        <th class="px-6 py-4 text-right">Total (₦)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-950/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-indigo-400">
                                #{{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-white">{{ $order->orderItems->count() }} item(s)</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-400 font-mono">
                                ₦{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $order->status_badge }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('shop.order-status', $order->order_number) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] rounded-lg transition-all">
                                    Track Order &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                You haven't placed any orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 bg-slate-950 border-t border-slate-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
