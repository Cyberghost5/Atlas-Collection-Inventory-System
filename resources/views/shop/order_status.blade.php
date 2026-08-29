@extends('layouts.storefront')

@section('title', 'Order Confirmation #' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    @if(session('success'))
        <div class="p-4 bg-emerald-950/80 border border-emerald-800 rounded-2xl text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Order Confirmation Card -->
    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-800 gap-4">
            <div>
                <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Atlas Collection Order</span>
                <h1 class="text-2xl font-black font-mono text-indigo-400 mt-0.5">#{{ $order->order_number }}</h1>
                <p class="text-xs text-slate-400 mt-1">Placed on {{ $order->created_at->format('M d, Y - h:i A') }}</p>
            </div>
            
            <div class="text-right">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $order->status_badge }}">
                    Status: {{ strtoupper($order->status) }}
                </span>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-slate-950 rounded-2xl p-5 border border-slate-800 space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Order Delivery Timeline</h4>
            <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold">
                <div class="p-2 rounded-xl {{ in_array($order->status, ['pending', 'processing', 'completed']) ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-slate-600' }}">
                    1. Order Placed
                </div>
                <div class="p-2 rounded-xl {{ in_array($order->status, ['processing', 'completed']) ? 'bg-indigo-600 text-white' : 'bg-slate-900 text-slate-600' }}">
                    2. Processing
                </div>
                <div class="p-2 rounded-xl {{ $order->status === 'completed' ? 'bg-emerald-600 text-white' : 'bg-slate-900 text-slate-600' }}">
                    3. Dispatched
                </div>
                <div class="p-2 rounded-xl {{ $order->status === 'completed' ? 'bg-emerald-600 text-white' : 'bg-slate-900 text-slate-600' }}">
                    4. Delivered
                </div>
            </div>
        </div>

        <!-- Purchased Apparel Items -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Itemized Summary</h4>
            <div class="divide-y divide-slate-800 border border-slate-800 rounded-2xl bg-slate-950/50 overflow-hidden">
                @foreach($order->orderItems as $item)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 overflow-hidden border border-slate-800">
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-white">{{ $item->product->name ?? 'Apparel Item' }}</h5>
                                <p class="text-[10px] text-slate-400">Size: <strong class="text-slate-200">{{ $item->product->size }}</strong> | Qty: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-400 font-mono">
                                ₦{{ number_format($item->subtotal, 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Shipping & Customer Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-800 text-xs">
            <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-500">Customer Contact</span>
                <p class="font-bold text-slate-200">{{ $order->customer_name }}</p>
                <p class="text-slate-400"><i class="fa-solid fa-phone text-slate-500 mr-1"></i> {{ $order->customer_phone }}</p>
                <p class="text-slate-400"><i class="fa-solid fa-envelope text-slate-500 mr-1"></i> {{ $order->customer_email }}</p>
            </div>

            <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-500">Delivery Address in Nigeria</span>
                <p class="text-slate-300 leading-relaxed">{{ $order->shipping_address }}</p>
            </div>
        </div>

        <!-- Total Price Card -->
        <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Total Order Amount</span>
            <span class="text-2xl font-black text-emerald-400 font-display">
                ₦{{ number_format($order->total_amount, 2) }}
            </span>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('shop.index') }}" class="text-xs font-bold text-slate-400 hover:text-white transition-colors">
                &larr; Continue Shopping
            </a>
            @auth
                <a href="{{ route('shop.my-orders') }}" class="px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-500 transition-all">
                    My Order History
                </a>
            @endauth
        </div>

    </div>

</div>
@endsection
