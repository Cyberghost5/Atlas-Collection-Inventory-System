@extends('layouts.app')

@section('title', 'Order Invoice #' . $order->order_number)
@section('page_title', 'Process Order #' . $order->order_number)
@section('page_subtitle', 'Customer invoice details, itemized catalog breakdown & delivery status updater')

@section('content')
<div class="space-y-6">

    <!-- Top Grid: Status Card & Customer Logistics Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Order Overview & Line Items -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400">Order Reference</span>
                        <h2 class="text-2xl font-black font-mono text-amber-600 dark:text-amber-400">#{{ $order->order_number }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Placed on {{ $order->created_at->format('M d, Y - h:i A') }}</p>
                    </div>

                    <div class="text-right space-y-2">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $order->status_badge }}">
                            Status: {{ strtoupper($order->status) }}
                        </span>
                        <div class="flex items-center space-x-2 pt-1">
                            <a href="{{ route('orders.receipt', ['order_number' => $order->order_number, 'format' => 'pos']) }}" target="_blank" class="px-3 py-1.5 bg-amber-500 text-slate-950 font-extrabold text-[11px] rounded-xl shadow hover:bg-amber-400 transition-all flex items-center space-x-1">
                                <span><i class="fa-solid fa-print mr-1"></i> POS Receipt (80mm)</span>
                            </a>
                            <a href="{{ route('orders.receipt', ['order_number' => $order->order_number, 'format' => 'a4']) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px] rounded-xl hover:bg-slate-200 transition-all">
                                <i class="fa-solid fa-file-invoice mr-1"></i> A4 Invoice
                            </a>
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                                if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '234' . substr($cleanPhone, 1);
                                }
                                $receiptUrl = route('orders.receipt', $order->order_number);
                                $waMsg = "Hello *{$order->customer_name}*!\nThank you for shopping at *Atlas Collection* (Bauchi Store).\n\n*SALES RECEIPT DETAILS*\nReceipt No: #{$order->order_number}\nDate: " . $order->created_at->format('M d, Y - h:i A') . "\nPayment Method: " . strtoupper(str_replace('_', ' ', $order->payment_method ?? 'Cash')) . "\n*Total Paid:* NGN " . number_format($order->total_amount, 2) . "\n\nView Digital Receipt Link:\n{$receiptUrl}\n\n*Atlas Collection*, Bauchi, Nigeria.\nThank you for your business!";
                                $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($waMsg) : "https://api.whatsapp.com/send?text=" . rawurlencode($waMsg);
                            @endphp
                            <a href="{{ $waUrl }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] rounded-xl transition-all shadow">
                                <span><i class="fa-brands fa-whatsapp mr-1 text-xs"></i> WhatsApp Receipt</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-bold text-emerald-800 dark:text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Order Items List -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Purchased Items ({{ optional($order->orderItems)->count() ?? 0 }})
                    </h3>
                    <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400">Total: ₦{{ number_format($order->total_amount, 2) }}</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($order->orderItems ?? [] as $item)
                        <div class="p-5 flex items-center justify-between">
                            <div class="space-y-1">
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $item->product->name ?? $item->product_name ?? 'Item' }}
                                </h4>
                                <div class="text-[11px] text-slate-400 font-mono space-x-2">
                                    <span>SKU: {{ $item->product->sku ?? 'N/A' }}</span>
                                    <span>• Size: {{ $item->product->size ?? 'Std' }}</span>
                                    @if(isset($item->product->color))
                                        <span>• Color: {{ $item->product->color }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="block font-black text-slate-900 dark:text-white font-mono text-sm">₦{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                                <span class="text-[11px] text-slate-400">₦{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-xs text-slate-500">
                            No line items recorded for this order.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Col: Status Transition Form & Customer Logistics Card -->
        <div class="space-y-6">

            <!-- Status Transition Form -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    Update Processing Status
                </h3>

                <form method="POST" action="{{ route('orders.update-status', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Select Status</label>
                        <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing / Packaging</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed / Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Notes / Waybill Ref</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Dispatched via GIG Logistics Waybill #9842..." 
                                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all">
                        Update Order Status
                    </button>
                </form>
            </div>

            <!-- Customer & Delivery Logistics Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    Customer Logistics (Nigeria)
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-semibold">Customer Name</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $order->customer_name }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-semibold">Phone Number</span>
                        <a href="tel:{{ $order->customer_phone }}" class="font-mono text-amber-600 dark:text-amber-400 font-bold hover:underline">
                            <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $order->customer_phone }}
                        </a>
                    </div>

                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-semibold">Email Address</span>
                        <a href="mailto:{{ $order->customer_email }}" class="text-slate-700 dark:text-slate-300 hover:underline">
                            <i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $order->customer_email }}
                        </a>
                    </div>

                    <div>
                        <span class="block text-[10px] text-slate-400 uppercase font-semibold">Payment Method</span>
                        <span class="font-bold text-slate-900 dark:text-white text-xs inline-flex items-center px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 rounded-lg mt-0.5 border border-amber-200 dark:border-amber-900">
                            @if(($order->payment_method ?? 'cash') === 'cash') <i class="fa-solid fa-money-bill-wave mr-1"></i> Cash Payment
                            @elseif(($order->payment_method ?? '') === 'bank_transfer') <i class="fa-solid fa-building-columns mr-1"></i> Bank Transfer
                            @elseif(($order->payment_method ?? '') === 'pos') <i class="fa-solid fa-credit-card mr-1"></i> POS / Card Machine
                            @else <i class="fa-solid fa-globe mr-1"></i> Other Payment @endif
                        </span>
                    </div>

                    @if(!empty($order->payment_proof))
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] text-slate-400 uppercase font-semibold mb-1">Uploaded Payment Proof</span>
                            @php
                                $ext = strtolower(pathinfo($order->payment_proof, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                <a href="{{ asset($order->payment_proof) }}" target="_blank" class="block group">
                                    <img src="{{ asset($order->payment_proof) }}" alt="Payment Proof Receipt" class="w-full h-36 object-cover rounded-xl border border-slate-200 dark:border-slate-700 group-hover:opacity-90 transition-opacity">
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold block mt-1">Click to view full receipt &rarr;</span>
                                </a>
                            @else
                                <a href="{{ asset($order->payment_proof) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all">
                                    <i class="fa-solid fa-file-pdf mr-1"></i> View Document Proof &rarr;
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="block text-[10px] text-slate-400 uppercase font-semibold">Shipping Address</span>
                        <p class="text-slate-800 dark:text-slate-200 font-medium mt-1 leading-relaxed bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                            {{ $order->shipping_address }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
