@extends('layouts.app')

@section('title', 'Low Stock Warnings')
@section('page_title', 'Low Stock & Restock Alert Manager')
@section('page_subtitle', 'Automated supplier reorder dispatcher and low stock warnings')

@section('content')
<div class="space-y-6">

    <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="p-3 bg-rose-100 dark:bg-rose-900/60 rounded-xl text-rose-600 dark:text-rose-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-rose-900 dark:text-rose-200">Critical Reorder Required ({{ $totalLowCount ?? count($products) }} Items)</h3>
                <p class="text-xs text-rose-700 dark:text-rose-300">Items below reach threshold level (&lt; 10 units). Send 1-click WhatsApp purchase orders directly to suppliers below.</p>
            </div>
        </div>
    </div>

    <!-- Supplier-Grouped Bulk WhatsApp Restock Dispatcher -->
    @if(isset($supplierGroups) && count($supplierGroups) > 0)
        <div class="space-y-3">
            <h4 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center">
                <i class="fa-solid fa-truck-ramp-box text-amber-500 mr-1.5"></i> Supplier Bulk Reorder Dispatcher
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($supplierGroups as $supplierName => $items)
                    @php
                        $supplierObj = $items->first()->supplier ?? null;
                        $cleanPhone = preg_replace('/[^0-9]/', '', $supplierObj->phone ?? '');
                        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '234' . substr($cleanPhone, 1);
                        }

                        $itemListMsg = "";
                        foreach ($items as $idx => $it) {
                            $reorderQty = max(10, ($it->min_stock_level * 2) - $it->stock_quantity);
                            $itemListMsg .= ($idx + 1) . ". {$it->name} (Size: {$it->size}, SKU: {$it->sku}) -> Current: {$it->stock_quantity} {$it->unit}s (Reorder Qty: {$reorderQty})\n";
                        }

                        $bulkMsg = "Hello *{$supplierName}*!\nBulk Restock Order from *Atlas Collection* (Bauchi Store):\n\n*LOW STOCK ITEMS TO REORDER:*\n{$itemListMsg}\nPlease confirm availability, lead time, and total invoice cost. Thank you!";
                        $waBulkUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($bulkMsg) : "https://api.whatsapp.com/send?text=" . rawurlencode($bulkMsg);
                    @endphp

                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-extrabold text-slate-900 dark:text-white font-display">{{ $supplierName }}</h5>
                                <p class="text-[10px] text-slate-400 font-mono"><i class="fa-solid fa-phone mr-1"></i> {{ $supplierObj->phone ?? 'No Phone Contact' }} • <i class="fa-solid fa-envelope mr-1"></i> {{ $supplierObj->email ?? 'No Email' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                {{ count($items) }} Low Stock Item(s)
                            </span>
                        </div>

                        <div class="text-xs space-y-1 text-slate-600 dark:text-slate-300 max-h-24 overflow-y-auto pr-1">
                            @foreach($items as $it)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="truncate max-w-[200px]" title="{{ $it->name }}">• {{ $it->name }} ({{ $it->size }})</span>
                                    <span class="font-mono font-bold text-rose-600 dark:text-rose-400">{{ $it->stock_quantity }} in stock</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <a href="{{ $waBulkUrl }}" target="_blank" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow transition-all flex items-center space-x-1">
                                <span><i class="fa-brands fa-whatsapp mr-1 text-sm"></i> Send Bulk WhatsApp Purchase Order</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Low Stock Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[800px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Product Record</th>
                        <th class="px-6 py-4 whitespace-nowrap">Category</th>
                        <th class="px-6 py-4 whitespace-nowrap">Supplier</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Current Stock</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Min Threshold</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-rose-50/40 dark:hover:bg-rose-950/20 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('products.show', $product) }}" class="hover:underline">
                                    {{ $product->name }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono">SKU: {{ $product->sku }} • Variant: {{ $product->size }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium whitespace-nowrap">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $product->supplier->name ?? 'No Supplier Assigned' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="px-3 py-1 bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-lg font-extrabold text-xs">
                                    {{ $product->stock_quantity }} {{ $product->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-semibold whitespace-nowrap">
                                {{ $product->min_stock_level }} {{ $product->unit }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($product->supplier && $product->supplier->phone)
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $product->supplier->phone);
                                        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '234' . substr($cleanPhone, 1);
                                        }
                                        $reorderQty = max(10, ($product->min_stock_level * 2) - $product->stock_quantity);
                                        $supplierMsg = rawurlencode("Hello {$product->supplier->name}!\nRestock Purchase Order from Atlas Collection (Bauchi Store):\n\nItem: {$product->name} (SKU: {$product->sku}, Size: {$product->size})\nCurrent Stock: {$product->stock_quantity} {$product->unit}(s)\nSuggested Reorder Qty: {$reorderQty} {$product->unit}(s)\n\nPlease confirm price quote and dispatch terms.");
                                        $supplierWaUrl = "https://wa.me/{$cleanPhone}?text={$supplierMsg}";
                                    @endphp
                                    <a href="{{ $supplierWaUrl }}" 
                                       target="_blank"
                                       title="Send Reorder Request on WhatsApp" 
                                       class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[11px] rounded-xl transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <span><i class="fa-brands fa-whatsapp mr-1 text-xs"></i> Reorder</span>
                                    </a>
                                @endif

                                <a href="{{ route('products.show', $product) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow transition-all">
                                    Log Stock
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-emerald-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No Low Stock Items!</p>
                                <p class="text-xs text-slate-400 mt-1">All products are currently above their warning thresholds.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
