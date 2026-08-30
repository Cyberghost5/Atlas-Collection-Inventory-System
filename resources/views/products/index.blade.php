@extends('layouts.app')

@section('title', 'Inventory')
@section('page_title', 'Atlas Collection')
@section('page_subtitle', 'Manage clothes, perfumes, shoes, bags, watches, jewelry, pricing, and stock levels')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search product, SKU, size, color..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Category</label>
                <select name="category_id" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Size Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Size / Variant</label>
                <select name="size" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Variants</option>
                    @foreach(['S', 'M', 'L', 'XL', 'XXL', 'EU 40', 'EU 41', 'EU 42', '50ml', '100ml', '40mm', 'Standard'] as $sz)
                        <option value="{{ $sz }}" {{ request('size') === $sz ? 'selected' : '' }}>Variant: {{ $sz }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Classification Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Classification</label>
                <select name="usage_type" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Stock Types</option>
                    <option value="retail" {{ request('usage_type') == 'retail' ? 'selected' : '' }}>Retail Inventory</option>
                    <option value="display_sample" {{ request('usage_type') == 'display_sample' ? 'selected' : '' }}>Display / Tester</option>
                    <option value="both" {{ request('usage_type') == 'both' ? 'selected' : '' }}>Dual Use</option>
                </select>
            </div>

            <!-- Rows Per Page -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Rows Per Page</label>
                <select name="per_page" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 Items</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Items</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Items</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Items</option>
                    <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 Items</option>
                    <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500 Items</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl transition-all shadow-sm">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'category_id', 'usage_type', 'size', 'low_stock']))
                    <a href="{{ route('products.index') }}" class="py-2 px-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Standalone Bulk Barcode Form (Decoupled to prevent HTML form nesting) -->
    <form id="bulk-barcodes-form" method="POST" action="{{ route('products.barcodes.print') }}" target="_blank">
        @csrf
    </form>

    <!-- Standalone Bulk Delete Form -->
    <form id="bulk-delete-form" method="POST" action="{{ route('products.bulk-destroy') }}">
        @csrf
        @method('DELETE')
    </form>

    <div x-data="{ 
            selectAll: false, 
            selectedCount: 0, 
            showDeleteModal: false,
            updateCount() { 
                this.selectedCount = document.querySelectorAll('input[name=\'product_ids[]\']:checked').length; 
            },
            submitBulkDelete() {
                let checkedInputs = document.querySelectorAll('input[name=\'product_ids[]\']:checked');
                let deleteForm = document.getElementById('bulk-delete-form');
                deleteForm.querySelectorAll('input[name=\'ids[]\']').forEach(el => el.remove());
                
                checkedInputs.forEach(input => {
                    let hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'ids[]';
                    hidden.value = input.value;
                    deleteForm.appendChild(hidden);
                });
                deleteForm.submit();
            }
        }" class="space-y-4">

        <!-- Bulk Barcode & Delete Action Bar -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
            <div class="flex items-center space-x-3">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Bulk Actions:</span>
                <span class="text-xs font-mono font-extrabold text-amber-600 dark:text-amber-400" x-text="selectedCount + ' Item(s) Selected'"></span>
            </div>

            <div class="flex items-center space-x-2">
                @if(auth()->user()->isAdmin())
                    <button type="button" 
                            x-show="selectedCount > 0"
                            x-cloak
                            @click="showDeleteModal = true" 
                            class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                        <i class="fa-solid fa-trash-can mr-1"></i>
                        <span>Delete Selected (<span x-text="selectedCount"></span>)</span>
                    </button>
                @endif

                <select form="bulk-barcodes-form" name="format" class="py-1.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    <option value="a4">A4 Sticker Sheet (3×8)</option>
                    <option value="thermal">80mm POS Thermal Roll</option>
                </select>
                <button type="submit" form="bulk-barcodes-form" class="px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                    <span><i class="fa-solid fa-barcode mr-1"></i> Bulk Print Price Tag Stickers</span>
                </button>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('export.products', request()->query()) }}" 
                       title="Export filtered inventory list to CSV"
                       class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                        <span><i class="fa-solid fa-file-csv mr-1"></i> Export CSV</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Bulk Delete Confirmation Modal -->
        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            
            <div @click.away="showDeleteModal = false" 
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-xs text-slate-800 dark:text-slate-200">
                
                <div class="flex items-center space-x-3 text-rose-600 dark:text-rose-400">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/10 flex items-center justify-center text-xl font-bold border border-rose-500/20">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Confirm Bulk Deletion</h3>
                        <p class="text-[10px] text-slate-400">Permanent inventory removal</p>
                    </div>
                </div>

                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 rounded-2xl border border-rose-200 dark:border-rose-900/60 text-slate-700 dark:text-slate-300 space-y-2">
                    <p class="font-bold text-rose-700 dark:text-rose-400">
                        Are you sure you want to delete <span x-text="selectedCount" class="font-black underline"></span> selected product(s)?
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        This action will permanently remove these items and their image files from the Atlas Collection inventory. This action cannot be undone.
                    </p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 transition-all">
                        Cancel
                    </button>
                    <button type="button" @click="submitBulkDelete()" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl shadow transition-all flex items-center space-x-1.5">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Yes, Delete <span x-text="selectedCount"></span> Item(s)</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors w-full max-w-full">
            <div class="overflow-x-auto w-full max-w-full">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[900px]">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-4 text-center w-10">
                                <input type="checkbox" @change="selectAll = !selectAll; document.querySelectorAll('input[name=\'product_ids[]\']').forEach(cb => cb.checked = selectAll); updateCount();" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                            </th>
                            <th class="px-6 py-4 min-w-[200px]">Product Record</th>
                            <th class="px-6 py-4 whitespace-nowrap">Variant & Details</th>
                            <th class="px-6 py-4 whitespace-nowrap">Category</th>
                            <th class="px-6 py-4 whitespace-nowrap">Stock Type</th>
                            <th class="px-6 py-4 text-right whitespace-nowrap">Cost / Retail</th>
                            <th class="px-6 py-4 text-center whitespace-nowrap">Stock Quantity</th>
                            <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                <!-- Checkbox -->
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" form="bulk-barcodes-form" name="product_ids[]" value="{{ $product->id }}" @change="updateCount()" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                </td>

                            <!-- Product Name & SKU -->
                            <td class="px-6 py-4">
                                <a href="{{ route('products.show', $product) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-500 transition-colors block">
                                    {{ $product->name }}
                                </a>
                                <div class="text-[10px] text-slate-400 flex items-center space-x-2 mt-0.5 whitespace-nowrap">
                                    <span class="font-mono bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">SKU: {{ $product->sku }}</span>
                                    @if($product->supplier)
                                        <span>Supplier: {{ $product->supplier->name }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Size & Color -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-black bg-slate-900 dark:bg-slate-800 text-white dark:text-amber-400 border border-slate-700">
                                    {{ $product->size }}
                                </span>
                                @if($product->color)
                                    <span class="text-xs text-slate-600 dark:text-slate-300 font-medium ml-2 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                                        <i class="fa-solid fa-palette text-amber-500 mr-1"></i> {{ $product->color }}
                                    </span>
                                @endif
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium whitespace-nowrap">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </td>

                            <!-- Usage Type Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->usage_type === 'display_sample')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        <i class="fa-solid fa-shirt mr-1"></i> Display / Tester
                                    </span>
                                @elseif($product->usage_type === 'retail')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <i class="fa-solid fa-bag-shopping mr-1"></i> Retail Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <i class="fa-solid fa-rotate mr-1"></i> Dual Usage
                                    </span>
                                @endif
                            </td>

                            <!-- Cost / Selling Price -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="font-semibold text-slate-900 dark:text-white font-mono">₦{{ number_format($product->cost_price, 2) }} <span class="text-[10px] text-slate-400 font-normal">cost</span></div>
                                @if($product->selling_price)
                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono font-medium">₦{{ number_format($product->selling_price, 2) }} <span class="text-[9px] text-slate-400 font-normal">retail</span></div>
                                @endif
                            </td>

                            <!-- Stock Quantity & Low Stock Badge -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($product->is_low_stock)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        <i class="fa-solid fa-triangle-exclamation mr-1 text-rose-500"></i>
                                        {{ $product->stock_quantity }} {{ $product->unit }} (Low)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $product->stock_quantity }} {{ $product->unit }}
                                    </span>
                                @endif
                                @if(!is_null($product->display_stock_quantity))
                                    <div class="text-[10px] text-amber-600 dark:text-amber-400 font-extrabold mt-0.5" title="Custom count shown to storefront visitors">
                                        Storefront: {{ $product->display_stock_quantity }}
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('products.barcode', $product) }}" 
                                   target="_blank" 
                                   title="Print Price Tag Barcode & QR Label"
                                   class="px-2.5 py-1.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-amber-400 font-bold text-[11px] rounded-lg transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <span><i class="fa-solid fa-tag text-amber-400 mr-1"></i> Tag</span>
                                </a>

                                @php
                                    $catalogLink = route('shop.show', $product->slug ?? $product->id);
                                    $staffMsg = rawurlencode("Hello! Here is the item from Atlas Collection catalog:\n\n*{$product->name}*\nSize: {$product->size}\nColor: " . ($product->color ?? 'Standard') . "\nPrice: NGN " . number_format($product->selling_price ?? $product->cost_price, 2) . "\n\nProduct Link: {$catalogLink}");
                                    $staffWaUrl = "https://api.whatsapp.com/send?text={$staffMsg}";
                                @endphp

                                <a href="{{ $staffWaUrl }}" 
                                   target="_blank" 
                                   title="Share Catalog Link to Customer WhatsApp"
                                   class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] rounded-lg transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                    </svg>
                                    <span>Share</span>
                                </a>

                                <a href="{{ route('products.show', $product) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-[11px] rounded-lg transition-all shadow-sm">
                                    Manage
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px] rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                    Edit
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete {{ $product->name }} from collection inventory?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold text-[11px]">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                No inventory records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
