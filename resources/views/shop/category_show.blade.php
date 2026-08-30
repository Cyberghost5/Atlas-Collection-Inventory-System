@extends('layouts.storefront')

@section('title', $category->name . ' Collection | Atlas Collection Bauchi')
@section('meta_title', $category->name . ' Collection - Atlas Collection Bauchi')
@section('meta_description', 'Shop ' . $category->name . ' at Atlas Collection in Bauchi. Browse available stock items with live sizes, colors, prices, and fast WhatsApp order checkout.')
@section('meta_keywords', $category->name . ' Bauchi, Buy ' . $category->name . ' Nigeria, Atlas Collection ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" 
     x-data="{
         page: 1,
         loadingMore: false,
         hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
         loadedCount: {{ $products->count() }},
         totalCount: {{ $products->total() }},
         quickModalOpen: false,
         modalProduct: {},
         quantity: 1,
         customerName: '',
         customerPhone: '',
         shippingAddress: '',
         sellerPhone: '{{ config('services.whatsapp.number') }}',
         
         openQuickOrder(prod) {
             this.modalProduct = prod;
             this.quantity = 1;
             this.quickModalOpen = true;
         },

         get modalTotal() {
             let price = this.modalProduct.selling_price || this.modalProduct.cost_price || 0;
             return (this.quantity * price).toLocaleString('en-NG', { minimumFractionDigits: 2 });
         },

         get modalWhatsappUrl() {
             let price = this.modalProduct.selling_price || this.modalProduct.cost_price || 0;
             let itemUrl = window.location.origin + '/shop/product/' + (this.modalProduct.slug || this.modalProduct.id);
             let text = `Hello Atlas Collection!\nI would like to quick order from your *${this.modalProduct.category ? this.modalProduct.category.name : 'Catalog'}* section:\n\n*Product:* ${this.modalProduct.name}\n*Size:* ${this.modalProduct.size || 'M'}\n*Color:* ${this.modalProduct.color || 'Standard'}\n*Unit Price:* NGN ${price.toLocaleString('en-NG')}\n*Quantity:* ${this.quantity}\n*Total Estimate:* NGN ${this.modalTotal}\n*SKU:* ${this.modalProduct.sku}\n*Product Link:* ${itemUrl}`;
             
             if (this.customerName.trim() !== '') {
                 text += `\n\n*Customer Name:* ${this.customerName.trim()}`;
             }
             if (this.customerPhone.trim() !== '') {
                 text += `\n*Contact Phone:* ${this.customerPhone.trim()}`;
             }
             if (this.shippingAddress.trim() !== '') {
                 text += `\n*Delivery Location:* ${this.shippingAddress.trim()}`;
             }

             return `https://wa.me/${this.sellerPhone}?text=${encodeURIComponent(text)}`;
         },

         async loadNextChunk() {
             if (this.loadingMore || !this.hasMore) return;
             this.loadingMore = true;
             this.page++;

             try {
                 let url = new URL(window.location.href);
                 url.searchParams.set('page', this.page);

                 let response = await fetch(url.toString(), {
                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                 });

                 if (!response.ok) throw new Error('Network error');

                 let data = await response.json();
                 
                 let container = document.getElementById('product-grid-container');
                 let tempDiv = document.createElement('div');
                 tempDiv.innerHTML = data.html;
                 
                 while (tempDiv.firstChild) {
                     container.appendChild(tempDiv.firstChild);
                 }

                 this.loadedCount += data.count;
                 this.hasMore = data.has_more;
             } catch (err) {
                 console.error('Failed loading product chunk:', err);
                 this.page--;
             } finally {
                 this.loadingMore = false;
             }
         }
     }">

    <!-- Breadcrumb & Header Banner -->
    <div class="space-y-4">
        <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('shop.index') }}" class="hover:text-amber-500 transition-colors">Home</a>
            <span>&rarr;</span>
            <!-- <a href="{{ route('shop.categories') }}" class="hover:text-amber-500 transition-colors">Categories</a>
            <span>&rarr;</span> -->
            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $category->name }}</span>
        </div>

        <div class="bg-gradient-to-r from-amber-600 to-amber-800 dark:from-slate-900 dark:to-slate-950 p-6 sm:p-8 rounded-3xl text-white shadow-xl border border-amber-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/10 dark:bg-amber-500/10 rounded-full text-xs font-extrabold text-amber-200 dark:text-amber-400 border border-white/20 dark:border-amber-500/20">
                    <i class="fa-solid fa-tag"></i>
                    <span>Category Collection</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black font-display tracking-tight uppercase">
                    {{ $category->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-100 dark:text-slate-300 leading-relaxed">
                    {{ $category->description ?? 'Browse our curated stock of ' . strtolower($category->name) . ' items in Bauchi, Nigeria. Order directly on WhatsApp.' }}
                </p>
            </div>

            <div class="flex items-center space-x-3 bg-white/10 dark:bg-slate-800/80 p-4 rounded-2xl border border-white/20 dark:border-slate-700 flex-shrink-0">
                <div class="text-center px-2">
                    <span class="block text-2xl font-black font-mono text-amber-300 dark:text-amber-400">{{ $products->total() }}</span>
                    <span class="text-[10px] uppercase font-bold text-slate-200 dark:text-slate-400">Total Items</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <form method="GET" action="{{ route('shop.category.show', $category->slug) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Search Input -->
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search inside {{ $category->name }}..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </div>

            <!-- Size Filter -->
            <div>
                <select name="size" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Available Sizes</option>
                    @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL', 'EU 39', 'EU 40', 'EU 41', 'EU 42', 'EU 43', 'EU 44', 'EU 45', '100ml', '50ml', 'Standard'] as $sz)
                        <option value="{{ $sz }}" {{ request('size') === $sz ? 'selected' : '' }}>Size {{ $sz }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">Sort: Newest Arrival</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Stock Available</option>
                </select>
            </div>

            <!-- Items Per Page Dropdown -->
            <div>
                <select name="per_page" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 Items / Page</option>
                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 Items / Page</option>
                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 Items / Page</option>
                    <option value="96" {{ request('per_page') == 96 ? 'selected' : '' }}>96 Items / Page</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl transition-all shadow-sm">
                    Apply Filter
                </button>
                @if(request()->hasAny(['search', 'size', 'sort', 'per_page']))
                    <a href="{{ route('shop.category.show', $category->slug) }}" class="px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-2xl hover:bg-slate-200 transition-all">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Product Cards Grid -->
    <div id="product-grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @if($products->count() > 0)
            @include('shop.partials.product_cards', ['products' => $products])
        @else
            <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 space-y-3">
                <i class="fa-solid fa-box-open text-4xl text-slate-400 block"></i>
                <p class="text-base font-bold text-slate-900 dark:text-white">No {{ $category->name }} items match your search</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Try adjusting your size or search keywords.</p>
            </div>
        @endif
    </div>

    <!-- Progressive 10-Item Chunk AJAX Control Bar -->
    <div class="pt-4 flex flex-col items-center justify-center space-y-3">
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center space-x-2">
            <span>Showing <strong class="text-slate-900 dark:text-white" x-text="loadedCount"></strong> of <strong class="text-amber-600 dark:text-amber-400 font-bold" x-text="totalCount"></strong> items</span>
            <template x-if="hasMore">
                <span class="inline-flex items-center text-[10px] text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20 font-bold animate-pulse">
                    <i class="fa-solid fa-bolt mr-1"></i> Loading 10 more items via AJAX...
                </span>
            </template>
        </div>

        <template x-if="hasMore">
            <button @click="loadNextChunk()" 
                    type="button" 
                    :disabled="loadingMore"
                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-2xl shadow-md transition-all flex items-center space-x-2 disabled:opacity-50">
                <svg x-show="loadingMore" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span><i class="fa-solid fa-rotate-right mr-1"></i> <span x-text="loadingMore ? 'Fetching Next 10 Items...' : 'Load 10 More Products'"></span></span>
            </button>
        </template>
    </div>

    <!-- QUICK WHATSAPP ORDER MODAL -->
    <div x-show="quickModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="quickModalOpen = false" 
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-xs text-slate-800 dark:text-slate-200 relative">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl font-bold">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Instant Order Checkout</h3>
                        <p class="text-[10px] text-slate-400">Direct order to seller via WhatsApp</p>
                    </div>
                </div>
                <button @click="quickModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl font-bold">&times;</button>
            </div>

            <!-- Product Brief Summary -->
            <div class="flex items-center space-x-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-700">
                <img :src="modalProduct.image_url" :alt="modalProduct.name" class="w-14 h-14 object-cover rounded-xl border border-slate-200 dark:border-slate-700 flex-shrink-0">
                <div class="overflow-hidden">
                    <h4 class="font-bold text-slate-900 dark:text-white truncate" x-text="modalProduct.name"></h4>
                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                        SKU: <span x-text="modalProduct.sku"></span> • Size: <span x-text="modalProduct.size || 'Std'"></span>
                    </p>
                    <p class="text-xs font-black text-amber-600 dark:text-amber-400 font-mono mt-0.5" 
                       x-text="'₦' + (modalProduct.selling_price || modalProduct.cost_price || 0).toLocaleString('en-NG')"></p>
                </div>
            </div>

            <!-- Inputs -->
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Select Quantity</label>
                    <div class="flex items-center space-x-3">
                        <button type="button" @click="if(quantity > 1) quantity--" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 font-bold text-slate-700 dark:text-white flex items-center justify-center hover:bg-slate-200 transition-all">-</button>
                        <span class="font-mono font-bold text-sm text-slate-900 dark:text-white" x-text="quantity"></span>
                        <button type="button" @click="if(quantity < (modalProduct.stock_quantity || 10)) quantity++" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 font-bold text-slate-700 dark:text-white flex items-center justify-center hover:bg-slate-200 transition-all">+</button>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Your Full Name (Optional)</label>
                    <input type="text" x-model="customerName" placeholder="e.g. Aminu Bello" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Your Phone Number (Optional)</label>
                    <input type="text" x-model="customerPhone" placeholder="0810 399 6947" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Delivery Address / Notes (Optional)</label>
                    <input type="text" x-model="shippingAddress" placeholder="e.g. Wunti Market Area, Bauchi" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Total Estimate -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between font-bold text-xs">
                <span class="text-slate-600 dark:text-slate-400">Total Estimate:</span>
                <span class="text-base font-black text-amber-600 dark:text-amber-400 font-mono" x-text="'₦' + modalTotal"></span>
            </div>

            <!-- WhatsApp Action Button -->
            <a :href="modalWhatsappUrl" 
               target="_blank" 
               @click="quickModalOpen = false"
               class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-2xl shadow-lg transition-all flex items-center justify-center space-x-2">
                <i class="fa-brands fa-whatsapp text-base"></i>
                <span>Send Quick Order to Seller &rarr;</span>
            </a>

        </div>
    </div>

</div>
@endsection
