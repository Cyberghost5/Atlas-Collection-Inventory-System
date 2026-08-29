<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Receipt #{{ $order->order_number }} - Atlas Collection</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-container { border: none !important; shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen font-sans text-slate-800 p-4 sm:p-8">

    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isStaff()))
        <!-- Admin & Staff Top Action Bar Controls (Hidden when printing) -->
        <div class="no-print max-w-2xl mx-auto mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('orders.show', $order) }}" class="text-xs font-bold text-slate-300 hover:text-white flex items-center space-x-1">
                    <span>&larr; Back to Order</span>
                </a>
                <span class="text-slate-600">|</span>
                <span class="text-xs font-bold text-amber-400 font-mono">Receipt #{{ $order->order_number }}</span>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Format Switcher -->
                <a href="{{ route('orders.receipt', ['order_number' => $order->order_number, 'format' => 'pos']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $format === 'pos' ? 'bg-amber-500 text-slate-950 shadow' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    80mm POS Thermal
                </a>
                <a href="{{ route('orders.receipt', ['order_number' => $order->order_number, 'format' => 'a4']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $format === 'a4' ? 'bg-amber-500 text-slate-950 shadow' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    A4 Standard Invoice
                </a>

                <!-- WhatsApp Share Button -->
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                    if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '234' . substr($cleanPhone, 1);
                    }
                    $receiptUrl = route('orders.receipt', $order->order_number);
                    $waMsg = "Hello *{$order->customer_name}*!\nThank you for shopping at *Atlas Collection* (Bauchi Store).\n\n*SALES RECEIPT DETAILS*\nReceipt No: #{$order->order_number}\nDate: " . $order->created_at->format('M d, Y - h:i A') . "\nPayment Method: " . strtoupper(str_replace('_', ' ', $order->payment_method ?? 'Cash')) . "\n*Total Paid:* NGN " . number_format($order->total_amount, 2) . "\n\nView Digital Receipt Link:\n{$receiptUrl}\n\n*Atlas Collection*, Bauchi, Nigeria.\nThank you for your business!";
                    $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($waMsg) : "https://api.whatsapp.com/send?text=" . rawurlencode($waMsg);
                @endphp

                <a href="{{ $waUrl }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow transition-all flex items-center space-x-1">
                    <span><i class="fa-brands fa-whatsapp mr-1 text-xs"></i> WhatsApp Receipt</span>
                </a>

                <!-- Print Button -->
                <button onclick="window.print()" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow transition-all flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Print</span>
                </button>
            </div>
        </div>
    @else
        <!-- Public Customer View Bar (No Admin Bar, Download/Print PDF Button Included) -->
        <div class="no-print max-w-2xl mx-auto mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-1.5 bg-amber-500/20 rounded-xl border border-amber-400/40">
                    <img src="{{ asset('logo.png') }}" alt="Atlas Collection" class="h-7 w-auto object-contain">
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-amber-400">Atlas Collection</h3>
                    <p class="text-[10px] text-slate-300">Official Digital Sales Receipt #{{ $order->order_number }}</p>
                </div>
            </div>

            <!-- Download / Save PDF Button for Public Customer -->
            <button onclick="window.print()" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download / Save PDF Receipt</span>
            </button>
        </div>
    @endif

    @if($format === 'pos')
        <!-- ================================================================= -->
        <!-- 🖨️ POS THERMAL RECEIPT FORMAT (80mm Width)                       -->
        <!-- ================================================================= -->
        <div class="print-container max-w-[340px] mx-auto bg-white p-6 rounded-2xl shadow-xl border border-slate-200 text-xs font-mono">
            <!-- Header -->
            <div class="text-center space-y-1 pb-4 border-b border-dashed border-slate-300">
                <img src="{{ asset('logo.png') }}" alt="Atlas Collection" class="h-12 w-auto mx-auto object-contain mb-2">
                <h1 class="text-base font-black font-display tracking-wider text-slate-900 uppercase">ATLAS COLLECTION</h1>
                <p class="text-[10px] text-slate-600 leading-tight">
                    Wunti Market, Bababa Plaza, Shop E7 Block E (Beside New Flyover), Bauchi, Nigeria
                </p>
                <p class="text-[10px] font-bold text-slate-800"><i class="fa-solid fa-phone text-slate-500 mr-1"></i> 0810 399 6947 • 08103996947</p>
            </div>

            <!-- Receipt Meta -->
            <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[11px]">
                <div class="flex justify-between">
                    <span class="text-slate-500">Receipt No:</span>
                    <span class="font-bold text-slate-900">#{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Date & Time:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Customer:</span>
                    <span class="font-bold text-slate-900 truncate max-w-[150px]">{{ $order->customer_name }}</span>
                </div>
                @if($order->customer_phone)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Phone:</span>
                        <span>{{ $order->customer_phone }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-slate-500">Payment:</span>
                    <span class="font-bold uppercase text-slate-900">{{ str_replace('_', ' ', $order->payment_method ?? 'Cash') }}</span>
                </div>
            </div>

            <!-- Items Table -->
            <div class="py-3 border-b border-dashed border-slate-300">
                <table class="w-full text-left text-[11px]">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase text-[9px]">
                            <th class="py-1">Item</th>
                            <th class="py-1 text-center">Qty</th>
                            <th class="py-1 text-right">Price</th>
                            <th class="py-1 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td class="py-2 pr-1">
                                    <span class="font-bold block text-slate-900 leading-tight">{{ $item->product->name ?? 'Product Item' }}</span>
                                    <span class="text-[9px] text-slate-500">Variant: {{ $item->product->size ?? 'M' }}</span>
                                </td>
                                <td class="py-2 text-center font-bold">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">₦{{ number_format($item->unit_price, 0) }}</td>
                                <td class="py-2 text-right font-bold text-slate-900">₦{{ number_format($item->subtotal, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="py-3 border-b border-dashed border-slate-300 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span>₦{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Tax (0%):</span>
                    <span>₦0.00</span>
                </div>
                <div class="flex justify-between text-sm font-black text-slate-900 pt-1 border-t border-slate-200">
                    <span>TOTAL PAID:</span>
                    <span>₦{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Footer Message -->
            <div class="pt-4 text-center space-y-1 text-[10px] text-slate-500">
                <p class="font-bold text-slate-900">Thank you for shopping with us!</p>
                <p>Goods sold in good condition are non-refundable.</p>
                <p class="pt-2 text-[9px] text-slate-400">Powered by Harkone Designs</p>
            </div>
        </div>

    @else
        <!-- ================================================================= -->
        <!-- 📄 STANDARD A4 INVOICE RECEIPT FORMAT                             -->
        <!-- ================================================================= -->
        <div class="print-container max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-slate-200 space-y-8">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b border-slate-200 pb-8">
                <div class="flex items-center space-x-4">
                    <div class="p-2 rounded-2xl border border-amber-400">
                        <img src="{{ asset('logo.png') }}" alt="Atlas Collection" class="h-14 w-auto object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl font-black font-display tracking-wider text-slate-900 uppercase">ATLAS COLLECTION</h1>
                        <p class="text-xs text-slate-500">Bauchi Retail & Wholesale Catalog Store</p>
                        <p class="text-xs text-slate-500 mt-1">Wunti Market, Bababa Plaza, Shop E7 Block E, Bauchi, Nigeria</p>
                    </div>
                </div>

                <div class="text-left sm:text-right space-y-1">
                    <span class="px-3 py-1 bg-amber-500 text-slate-950 font-black text-xs rounded-lg uppercase tracking-wider">OFFICIAL RECEIPT</span>
                    <h3 class="text-lg font-mono font-bold text-slate-900 mt-2">#{{ $order->order_number }}</h3>
                    <p class="text-xs text-slate-500">Date: {{ $order->created_at->format('F d, Y - h:i A') }}</p>
                </div>
            </div>

            <!-- Customer & Payment Metadata Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200 text-xs">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block mb-1">Billed To Customer</span>
                    <h4 class="text-sm font-bold text-slate-900">{{ $order->customer_name }}</h4>
                    <p class="text-slate-600 mt-1"><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $order->customer_phone ?? 'N/A' }}</p>
                    <p class="text-slate-600"><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $order->customer_email ?? 'N/A' }}</p>
                    @if($order->shipping_address)
                        <p class="text-slate-500 mt-2"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> {{ $order->shipping_address }}</p>
                    @endif
                </div>

                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block mb-1">Payment & Fulfillment Info</span>
                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Payment Method:</span>
                            <span class="font-bold text-slate-900 uppercase">{{ str_replace('_', ' ', $order->payment_method ?? 'Cash') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Payment Status:</span>
                            <span class="font-bold text-emerald-600 uppercase">{{ strtoupper($order->payment_status ?? 'Paid') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Order Status:</span>
                            <span class="font-bold text-indigo-600 uppercase">{{ strtoupper($order->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itemized Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-900 text-white uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Item Description</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Unit Price (₦)</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Subtotal (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-900 block text-sm">{{ $item->product->name ?? 'Product Item' }}</span>
                                    <span class="text-slate-400 text-[10px] font-mono">Variant: {{ $item->product->size ?? 'Standard' }} • Category: {{ $item->product->category->name ?? 'General' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right font-mono font-semibold">₦{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">₦{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Grand Totals & Signature -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 pt-6 border-t border-slate-200">
                <div class="text-xs text-slate-500 max-w-sm space-y-1">
                    <p class="font-bold text-slate-900">Terms & Conditions:</p>
                    <p>Thank you for choosing Atlas Collection! Items inspected and delivered in good condition are eligible for exchange within 24 hours with receipt.</p>
                </div>

                <div class="w-full sm:w-64 bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-mono">₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Delivery / Tax:</span>
                        <span class="font-mono">₦0.00</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>TOTAL:</span>
                        <span class="font-mono text-emerald-600">₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>
    @endif

</body>
</html>
