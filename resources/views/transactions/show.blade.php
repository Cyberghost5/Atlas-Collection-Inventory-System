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
                            <i class="fa-solid fa-print mr-1"></i> Thermal POS Receipt
                        </a>
                        <a href="{{ route('orders.receipt', ['order_number' => $transaction->order->order_number, 'format' => 'a4']) }}" target="_blank" class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px] rounded-xl hover:bg-slate-200 transition-all">
                            <i class="fa-solid fa-file-invoice mr-1"></i> A4 Invoice
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-2">
            <div>
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Payment Method</span>
                <span class="font-bold text-slate-900 dark:text-white inline-flex items-center px-2.5 py-1 rounded-lg mt-0.5 {{ $transaction->payment_method_badge }}">
                    @if($transaction->payment_method === 'cash') <i class="fa-solid fa-money-bill-wave mr-1"></i> Cash Payment
                    @elseif($transaction->payment_method === 'bank_transfer') <i class="fa-solid fa-building-columns mr-1"></i> Bank Transfer
                    @elseif($transaction->payment_method === 'pos') <i class="fa-solid fa-credit-card mr-1"></i> POS / Card Machine
                    @else <i class="fa-solid fa-globe mr-1"></i> Other Payment @endif
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
                    <a href="{{ route('orders.show', $transaction->order->order_number) }}" class="font-mono font-extrabold text-amber-600 dark:text-amber-400 hover:underline mt-0.5 block">
                        #{{ $transaction->order->order_number }}
                    </a>
                @else
                    <span class="text-slate-400 mt-0.5 block">Direct Transaction</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Customer & Operator Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-2">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Customer Details
            </h4>
            <div class="space-y-1 text-xs">
                <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $transaction->customer_name }}</p>
                <p class="text-slate-500 dark:text-slate-400 font-mono"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $transaction->customer_phone ?? 'No Phone' }}</p>
                <p class="text-slate-400"><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $transaction->customer_email ?? 'No Email' }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-2">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Cashier / Recorded By
            </h4>
            <div class="space-y-1 text-xs">
                <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $transaction->user->name ?? 'System Cashier' }}</p>
                <p class="text-slate-500 dark:text-slate-400 font-mono">{{ $transaction->user->email ?? '' }}</p>
                <p class="text-slate-400 uppercase font-bold text-[10px]">Role: {{ $transaction->user->role ?? 'Staff' }}</p>
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
                    <i class="fa-solid fa-file-pdf mr-1"></i> View PDF / Document Proof &rarr;
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
