@extends('layouts.app')

@section('title', 'Payment Transaction #' . $transaction->transaction_number)
@section('page_title', 'Payment Transaction Details')
@section('page_subtitle', 'Comprehensive audit overview for payment #' . $transaction->transaction_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center space-x-1">
            <span>&larr; Back to Transactions Ledger</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <span class="text-[10px] font-extrabold text-amber-500 uppercase tracking-widest block">Payment Record</span>
                <h3 class="text-xl font-black text-slate-900 dark:text-white font-mono mt-0.5">#{{ $transaction->transaction_number }}</h3>
                <p class="text-xs text-slate-400 mt-1">Logged on {{ $transaction->created_at->format('F d, Y - h:i:s A') }}</p>
            </div>

            <div class="text-right space-y-2">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Payment Amount</span>
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono">₦{{ number_format($transaction->amount, 2) }}</span>
                @if($transaction->order)
                    <div class="flex items-center justify-end space-x-2 pt-1">
                        <a href="{{ route('orders.receipt', ['order_number' => $transaction->order->order_number, 'format' => 'pos']) }}" target="_blank" class="px-3 py-1 bg-amber-500 text-slate-950 font-black text-[11px] rounded-xl shadow hover:bg-amber-400 transition-all">
                            🖨️ Thermal POS Receipt
                        </a>
                        <a href="{{ route('orders.receipt', ['order_number' => $transaction->order->order_number, 'format' => 'a4']) }}" target="_blank" class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px] rounded-xl hover:bg-slate-200 transition-all">
                            📄 A4 Invoice
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-2">
            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Payment Method</span>
                <span class="font-bold text-slate-900 dark:text-white inline-flex items-center px-2.5 py-1 rounded-lg mt-0.5 {{ $transaction->payment_method_badge }}">
                    @if($transaction->payment_method === 'cash') 💵 Cash Payment
                    @elseif($transaction->payment_method === 'bank_transfer') 💳 Bank Transfer
                    @elseif($transaction->payment_method === 'pos') 📱 POS / Card Machine
                    @else 🌐 Other Payment @endif
                </span>
            </div>

            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Payment Status</span>
                <span class="font-bold text-slate-900 dark:text-white inline-flex items-center px-2 py-0.5 rounded-full text-[10px] uppercase mt-0.5 {{ $transaction->payment_status_badge }}">
                    {{ strtoupper($transaction->payment_status) }}
                </span>
            </div>

            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Linked Order Reference</span>
                @if($transaction->order)
                    <a href="{{ route('orders.show', $transaction->order->order_number) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline text-sm block mt-0.5">
                        #{{ $transaction->order->order_number }} &rarr;
                    </a>
                @else
                    <span class="text-slate-400 font-mono block mt-0.5">N/A</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Staff & Customer Logistics Breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        
        <!-- Staff Logger Details -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-3">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                Logged By Staff / User
            </h4>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-slate-950 dark:bg-slate-800 text-amber-400 font-black flex items-center justify-center text-sm shadow">
                    {{ strtoupper(substr($transaction->staff->name ?? 'System', 0, 2)) }}
                </div>
                <div>
                    <h5 class="text-sm font-bold text-slate-900 dark:text-white">{{ $transaction->staff->name ?? 'System / Online Storefront' }}</h5>
                    <p class="text-xs text-amber-500 font-bold uppercase tracking-wider">{{ strtoupper($transaction->staff->role ?? 'Customer Checkout') }}</p>
                    <p class="text-[11px] text-slate-400">{{ $transaction->staff->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-3">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                Customer Details
            </h4>
            <div class="space-y-1 text-xs">
                <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $transaction->customer_name }}</p>
                <p class="text-slate-500 dark:text-slate-400 font-mono">📞 {{ $transaction->customer_phone ?? 'No Phone' }}</p>
                <p class="text-slate-400">✉️ {{ $transaction->customer_email ?? 'No Email' }}</p>
            </div>
        </div>

    </div>

    <!-- Payment Proof Uploaded File (If present) -->
    @if(!empty($transaction->payment_proof))
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-3">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                Uploaded Receipt Payment Proof
            </h4>

            @php
                $ext = strtolower(pathinfo($transaction->payment_proof, PATHINFO_EXTENSION));
            @endphp

            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                <a href="{{ asset($transaction->payment_proof) }}" target="_blank" class="block group">
                    <img src="{{ asset($transaction->payment_proof) }}" alt="Payment Proof Receipt" class="max-h-72 object-contain rounded-xl border border-slate-200 dark:border-slate-700 group-hover:opacity-95 transition-opacity">
                    <span class="text-xs text-amber-600 font-bold block mt-2">Click image to open full resolution receipt proof &rarr;</span>
                </a>
            @else
                <a href="{{ asset($transaction->payment_proof) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all">
                    📄 View PDF / Document Proof &rarr;
                </a>
            @endif
        </div>
    @endif

    <!-- Linked Order Items Summary -->
    @if($transaction->order && $transaction->order->orderItems->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-3">
            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                Apparel Items Purchased in Order #{{ $transaction->order->order_number }}
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-semibold text-[10px]">
                        <tr>
                            <th class="px-4 py-3">Apparel Item</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Unit Price (₦)</th>
                            <th class="px-4 py-3 text-right">Subtotal (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($transaction->order->orderItems as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-900 dark:text-white block">{{ $item->product->name ?? 'Apparel Item' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">Size: {{ $item->product->size ?? 'M' }} • SKU: {{ $item->product->sku ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right font-mono font-semibold">₦{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-white">₦{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
