@extends('layouts.storefront')

@section('title', 'Atlas Collection | Premium Clothes, Perfumes, Shoes, Bags, Watches & Jewelry in Bauchi')
@section('meta_title', 'Atlas Collection - Premium Clothes, Perfumes, Shoes, Bags, Watches & Jewelry in Bauchi')
@section('meta_description', 'Explore the live stock catalog of Atlas Collection in Bauchi, Nigeria. Shop luxury unisex clothes, designer perfumes, shoes, bags, watches, and jewelry at Wunti Market, Bababa Plaza, Shop E7 Block E.')
@section('meta_keywords', 'Atlas Collection, Atlas Collection Bauchi, clothes Bauchi, perfumes Bauchi, shoes Bauchi, bags Bauchi, watches Bauchi, jewelry Bauchi, Wunti Market store Bauchi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8"
     x-data="{
         quickModalOpen: false,
         selectedProduct: null,
         modalQuantity: 1,
         customerName: '',
         deliveryNote: '',
         sellerPhone: '{{ config('services.whatsapp.number') }}',
         copiedToast: false,

         currentPage: {{ $products->currentPage() }},
         hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
         loadingMore: false,
         loadedCount: {{ $products->count() }},
         totalCount: {{ $products->total() }},

         openQuickModal(product) {
             this.selectedProduct = product;
             this.modalQuantity = 1;
             this.customerName = '';
             this.deliveryNote = '';
             this.quickModalOpen = true;
         },

         copyItemLink(url) {
             navigator.clipboard.writeText(url);
             this.copiedToast = true;
             setTimeout(() => { this.copiedToast = false; }, 3000);
         },

         get modalTotalEstimate() {
             if (!this.selectedProduct) return '0.00';
             let price = parseFloat(this.selectedProduct.selling_price || this.selectedProduct.cost_price);
             return (price * this.modalQuantity).toLocaleString('en-NG', { minimumFractionDigits: 2 });
         },

         get modalWhatsappUrl() {
             if (!this.selectedProduct) return '#';
             let price = parseFloat(this.selectedProduct.selling_price || this.selectedProduct.cost_price);
             let itemUrl = window.location.origin + '/shop/product/' + this.selectedProduct.slug;
             let text = `Hello Atlas Collection!\nI would like to order the following apparel item from your Bauchi stock catalog:\n\n*Product:* ${this.selectedProduct.name}\n*Size:* ${this.selectedProduct.size}\n*Color:* ${this.selectedProduct.color || 'Standard'}\n*Unit Price:* NGN ${price.toLocaleString('en-NG')}\n*Quantity:* ${this.modalQuantity}\n*Total Estimate:* NGN ${this.modalTotalEstimate}\n*SKU:* ${this.selectedProduct.sku}\n*Product Link:* ${itemUrl}`;
             
             if (this.customerName.trim() !== '') {
                 text += `\n\n*Customer Name:* ${this.customerName.trim()}`;
             }
             if (this.deliveryNote.trim() !== '') {
                 text += `\n*Location/Notes:* ${this.deliveryNote.trim()}`;
             }
             return `https://wa.me/${this.sellerPhone}?text=${encodeURIComponent(text)}`;
         },

         loadNextChunk() {
             if (!this.hasMore || this.loadingMore) return;
             this.loadingMore = true;

             let url = new URL(window.location.href);
             url.searchParams.set('page', this.currentPage + 1);

             fetch(url.toString(), {
                 headers: {
                     'X-Requested-With': 'XMLHttpRequest',
                     'Accept': 'application/json'
                 }
             })
             .then(res => res.json())
             .then(data => {
                 if (data.html) {
                     document.getElementById('product-grid').insertAdjacentHTML('beforeend', data.html);
                     this.currentPage = data.next_page - 1;
                     this.hasMore = data.has_more;
                     this.loadedCount += data.count;
                 }
             })
             .catch(err => console.error('Error streaming product chunk:', err))
             .finally(() => {
                 this.loadingMore = false;
                 // Progressively fetch next page 10 at a time until all products are loaded
                 if (this.hasMore) {
                     setTimeout(() => { this.loadNextChunk(); }, 400);
                 }
             });
         },

         init() {
             if (this.hasMore) {
                 setTimeout(() => { this.loadNextChunk(); }, 600);
             }
         }
     }">

    <!-- Toast Notification for Copied Link -->
    <div x-show="copiedToast" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl border border-slate-700 flex items-center space-x-3 text-xs font-bold"
         style="display: none;">
        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Catalog Item Link Copied to Clipboard! <i class="fa-solid fa-clipboard-check text-emerald-400 ml-1"></i></span>
    </div>

    <!-- Hero Stock Preview Banner -->
    <div class="relative bg-gradient-to-r from-amber-600 via-slate-900 to-amber-700 dark:from-amber-950 dark:via-slate-950 dark:to-amber-950 rounded-3xl p-8 sm:p-12 border border-amber-500/30 shadow-xl overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8 text-white">
        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative z-10 max-w-xl space-y-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-amber-400/20 text-amber-200 dark:text-amber-300 border border-amber-400/30">
                <i class="fa-solid fa-star text-amber-300 mr-1.5"></i> LIVE BAUCHI CATALOG 2026
            </span>
            <h1 class="text-3xl sm:text-5xl font-black font-display tracking-tight text-white leading-tight">
                ATLAS <span class="text-amber-400">COLLECTION</span>
            </h1>
            <p class="text-sm font-serif italic text-amber-200/90 font-medium">
                ...your style, our identity
            </p>
            <p class="text-xs sm:text-sm text-slate-200 dark:text-slate-300 leading-relaxed pt-1">
                Browse our real-time stock catalog. Located at <strong>Wunti Market, Bababa Plaza, Block E, Shop E7 (Beside New Flyover), Bauchi</strong>. Select your item and click <strong>"WhatsApp Order"</strong> to send your pick directly to us!
            </p>
        </div>
        <div class="relative z-10 flex-shrink-0">
            <div class="p-3 bg-white rounded-3xl shadow-2xl border-2 border-amber-400/50">
                <img src="{{ asset('logo.png') }}" alt="Atlas Collection Logo" class="h-24 sm:h-32 w-auto object-contain">
            </div>
        </div>
    </div>

    <!-- Category Visual Spotlight Bar -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Featured Collection Categories</h3>
            <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold">1-Click Quick Filter</span>
        </div>
        <div class="flex items-center space-x-2.5 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('shop.index') }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all border flex items-center space-x-2 {{ !request('category_id') ? 'bg-amber-500 text-slate-950 border-amber-400 shadow-md font-black' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-amber-400' }}">
                <span><i class="fa-solid fa-fire text-amber-500 mr-1"></i> All Collections</span>
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('shop.index', array_merge(request()->except('category_id', 'page'), ['category_id' => $cat->id])) }}" 
                   class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all border flex items-center space-x-2 {{ request('category_id') == $cat->id ? 'bg-amber-500 text-slate-950 border-amber-400 shadow-md font-black' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-amber-400' }}">
                    <span>{{ $cat->name }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ request('category_id') == $cat->id ? 'bg-slate-950 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                        {{ $cat->products_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Search & Advanced Filter Controls -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
        <form method="GET" action="{{ route('shop.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Search Products & Catalog</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search perfume, shoes, bags, watches, clothes..." 
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-amber-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Category</label>
                <select name="category_id" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Size Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Size</label>
                <select name="size" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">All Sizes</option>
                    @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'] as $sz)
                        <option value="{{ $sz }}" {{ request('size') === $sz ? 'selected' : '' }}>Size: {{ $sz }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By Dropdown -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Sort By</label>
                <select name="sort" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Highest Stock</option>
                </select>
            </div>

            <!-- In-Stock Filter Checkbox & Submit Button -->
            <div class="flex flex-col justify-end space-y-2">
                <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer pb-1">
                    <input type="checkbox" name="in_stock_only" value="1" {{ request('in_stock_only') ? 'checked' : '' }} 
                           class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-amber-500 focus:ring-amber-500">
                    <span>In-Stock Only</span>
                </label>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="w-full py-2.5 px-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-sm transition-all">
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'size', 'sort', 'in_stock_only']))
                        <a href="{{ route('shop.index') }}" class="py-2.5 px-2 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <!-- Stock Catalog Grid -->
    <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @if($products->count() > 0)
            @include('shop.partials.product_cards', ['products' => $products])
        @else
            <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-base font-bold text-slate-900 dark:text-white">No apparel items match your search</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try selecting a different size or resetting filters.</p>
            </div>
        @endif
    </div>

    <!-- Progressive 10-Item Chunk AJAX Status & Control Bar -->
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
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 relative transition-colors">
            
            <!-- Close Button -->
            <button @click="quickModalOpen = false" class="absolute top-4 right-4 p-2 rounded-xl text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Modal Header with Selected Product -->
            <template x-if="selectedProduct">
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <img :src="selectedProduct.image_url || '{{ asset('logo.png') }}'" x-on:error="$el.src='{{ asset('logo.png') }}'" :alt="selectedProduct.name" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-amber-600 dark:text-amber-400">Quick WhatsApp Order</span>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight" x-text="selectedProduct.name"></h3>
                            <p class="text-xs text-slate-500">Size: <strong class="text-slate-800 dark:text-slate-200" x-text="selectedProduct.size"></strong> | Color: <strong class="text-slate-800 dark:text-slate-200" x-text="selectedProduct.color || 'Standard'"></strong></p>
                        </div>
                    </div>

                    <!-- Quantity Stepper -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Select Quantity</label>
                        <div class="flex items-center space-x-3">
                            <button @click="if (modalQuantity > 1) modalQuantity--" type="button" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 font-black border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white hover:bg-slate-200 dark:hover:bg-slate-700">-</button>
                            <input type="number" x-model.number="modalQuantity" min="1" :max="selectedProduct.stock_quantity" class="w-16 text-center py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            <button @click="if (modalQuantity < selectedProduct.stock_quantity) modalQuantity++" type="button" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 font-black border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white hover:bg-slate-200 dark:hover:bg-slate-700">+</button>
                            <span class="text-xs text-slate-500">Available: <strong class="text-slate-800 dark:text-slate-200" x-text="selectedProduct.stock_quantity + ' ' + selectedProduct.unit"></strong></span>
                        </div>
                    </div>

                    <!-- Optional Name & Location -->
                    <div class="space-y-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Your Name (Optional)</label>
                            <input type="text" x-model="customerName" placeholder="e.g. Ibrahim Bauchi" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Delivery Address / Notes (Optional)</label>
                            <input type="text" x-model="deliveryNote" placeholder="e.g. Near Central Mosque, Bauchi" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
                        </div>
                    </div>

                    <!-- Total Price Calculation -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Total Estimate:</span>
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400 font-display">₦<span x-text="modalTotalEstimate"></span></span>
                    </div>

                    <!-- WhatsApp CTA -->
                    <a :href="modalWhatsappUrl" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-emerald-600/20 text-center transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                        <span>Send Selection to WhatsApp &rarr;</span>
                    </a>
                </div>
            </template>

        </div>
    </div>

</div>
@endsection
