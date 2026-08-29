@extends('layouts.storefront')

@section('title', $product->name . ' | Atlas Collection Bauchi')
@section('meta_title', $product->name . ' (Size ' . $product->size . ') - Atlas Collection Bauchi')
@section('meta_description', 'Buy ' . $product->name . ' in Bauchi at Atlas Collection. Size: ' . $product->size . ', Color: ' . ($product->color ?? 'Standard') . '. Price: NGN ' . number_format($product->selling_price ?? $product->cost_price, 2) . '. Wunti Market, Bababa Plaza, Shop E7 Block E.')
@section('meta_image', $product->image_url)
@section('meta_keywords', $product->name . ', ' . ($product->category->name ?? 'Apparel') . ' Bauchi, ' . $product->size . ' ' . $product->name . ', Atlas Collection Bauchi')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ addslashes($product->name) }}",
  "image": [
    "{{ $product->image_url }}"
  ],
  "description": "{{ addslashes($product->description ?? 'Luxury unisex garment available at Atlas Collection Bauchi.') }}",
  "sku": "{{ $product->sku }}",
  "category": "{{ $product->category->name ?? 'Apparel' }}",
  "brand": {
    "@type": "Brand",
    "name": "Atlas Collection"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "NGN",
    "price": "{{ $product->selling_price ?? $product->cost_price }}",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "{{ $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
    "seller": {
      "@type": "Organization",
      "name": "Atlas Collection"
    }
  }
}
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" 
     x-data="{ 
         quantity: 1, 
         maxStock: {{ $product->stock_quantity }}, 
         unitPrice: {{ $product->selling_price ?? $product->cost_price }},
         customerName: '',
         customerPhone: '',
         deliveryNote: '',
         sellerPhone: '{{ config('services.whatsapp.number') }}',
         copiedToast: false,
         
         copyItemLink() {
             navigator.clipboard.writeText(window.location.href);
             this.copiedToast = true;
             setTimeout(() => { this.copiedToast = false; }, 3000);
         },

         get totalEstimate() {
             return (this.quantity * this.unitPrice).toLocaleString('en-NG', { minimumFractionDigits: 2 });
         },

         get whatsappLink() {
             let itemUrl = window.location.href;
             let text = `Hello Atlas Collection!\nI would like to order the following apparel item from your Bauchi stock catalog:\n\n*Product:* {{ addslashes($product->name) }}\n*Size:* {{ $product->size }}\n*Color:* {{ addslashes($product->color ?? 'Standard') }}\n*Unit Price:* NGN ${this.unitPrice.toLocaleString('en-NG')}\n*Quantity:* ${this.quantity}\n*Total Estimate:* NGN ${this.totalEstimate}\n*SKU:* {{ $product->sku }}\n*Product Link:* ${itemUrl}`;
             
             if (this.customerName.trim() !== '') {
                 text += `\n\n*Customer Name:* ${this.customerName.trim()}`;
             }
             if (this.customerPhone.trim() !== '') {
                 text += `\n*Contact Phone:* ${this.customerPhone.trim()}`;
             }
             if (this.deliveryNote.trim() !== '') {
                 text += `\n*Notes/Location:* ${this.deliveryNote.trim()}`;
             }
             
             return `https://wa.me/${this.sellerPhone}?text=${encodeURIComponent(text)}`;
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
        <span>Direct Item Link Copied to Clipboard! <i class="fa-solid fa-clipboard-check text-emerald-400 ml-1"></i></span>
    </div>

    <!-- Breadcrumbs & Share Action -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('shop.index') }}" class="hover:text-amber-500 transition-colors">Home</a>
            <span>&rarr;</span>
            <!-- <a href="{{ route('shop.categories') }}" class="hover:text-amber-500 transition-colors">Categories</a>
            <span>&rarr;</span> -->
            @if($product->category)
                <a href="{{ route('shop.category.show', $product->category->slug) }}" class="hover:text-amber-500 font-semibold transition-colors">{{ $product->category->name }}</a>
            @else
                <span class="text-slate-700 dark:text-slate-200 font-semibold">Apparel</span>
            @endif
            <span>&rarr;</span>
            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $product->name }}</span>
        </div>

        <button @click="copyItemLink()" 
                type="button"
                class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold transition-all flex items-center space-x-1.5 border border-slate-200 dark:border-slate-700">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span>Share / Copy Link</span>
        </button>
    </div>

    <!-- Product Grid & WhatsApp Order Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        <!-- Left Column: High-Res Stock Photo & Specifications -->
        <div class="space-y-6">
            
            <!-- Picture Display -->
            <div class="relative aspect-square bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-md transition-colors">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                
                <div class="absolute top-4 left-4 bg-slate-900/90 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-black text-white border border-slate-700">
                    Size: {{ $product->size }}
                </div>

                @if($product->color)
                    <div class="absolute top-4 right-4 bg-slate-900/90 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 border border-slate-700">
                        <i class="fa-solid fa-palette text-amber-400 mr-1"></i> {{ $product->color }}
                    </div>
                @endif
            </div>

            <!-- Available Variants of the Same Line -->
            @if($variants->count() > 0)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 space-y-3 shadow-sm transition-colors">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Available Size Variants</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('shop.show', $product->slug ?? $product->id) }}" class="px-3.5 py-2 rounded-xl text-xs font-black bg-amber-500 text-slate-950 shadow-sm">
                            Size {{ $product->size }} (Selected)
                        </a>
                        @foreach($variants as $variant)
                            <a href="{{ route('shop.show', $variant->slug ?? $variant->id) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:border-amber-400 transition-all">
                                Size {{ $variant->size }} ({{ $variant->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }})
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Item Description & Details -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 space-y-3 shadow-sm transition-colors">
                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Item Specifications</h4>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    {{ $product->description ?? 'Crafted with premium heavyweight organic cotton. Tailored for relaxed unisex streetwear styling.' }}
                </p>
                <div class="grid grid-cols-2 gap-3 pt-2 text-[11px] border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                    <div><span class="text-slate-400">SKU Code:</span> <span class="font-mono text-slate-800 dark:text-slate-200 font-bold">{{ $product->sku }}</span></div>
                    <div><span class="text-slate-400">Category:</span> <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $product->category->name ?? 'Unisex' }}</span></div>
                    <div><span class="text-slate-400">Stock Availability:</span> <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">{{ $product->stock_quantity }} {{ $product->unit }}(s) left</span></div>
                    <div><span class="text-slate-400">Location:</span> <span class="text-slate-800 dark:text-slate-200">Bauchi, Nigeria</span></div>
                </div>
            </div>

        </div>

        <!-- Right Column: WhatsApp Direct Order Panel -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-lg space-y-6 transition-colors">
            
            <!-- Header & Price -->
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30">
                    <i class="fa-brands fa-whatsapp mr-1.5 text-sm"></i> DIRECT WHATSAPP ORDER
                </span>
                <h2 class="text-2xl sm:text-3xl font-black font-display text-slate-900 dark:text-white mt-2">{{ $product->name }}</h2>
                <div class="mt-3 flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-amber-600 dark:text-amber-400 font-display">
                        ₦{{ number_format($product->selling_price ?? $product->cost_price, 2) }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">per unit</span>
                </div>
            </div>

            <!-- Stock Alert Box -->
            @if($product->stock_quantity < 1)
                <div class="p-4 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-700 dark:text-rose-400 text-xs font-bold">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 mr-1.5"></i> Currently Out of Stock. You can still message us on WhatsApp to inquire about re-stock dates!
                </div>
            @else
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-emerald-800 dark:text-emerald-400 text-xs font-medium flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>In Stock — Ready for immediate pickup at Bauchi store or delivery</span>
                </div>
            @endif

            <!-- Interactive Quantity & Customer Details Form -->
            <div class="space-y-4 pt-2">
                
                <!-- Quantity Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Quantity Needed</label>
                    <div class="flex items-center space-x-3">
                        <button @click="if (quantity > 1) quantity--" 
                                type="button"
                                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white font-black text-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            -
                        </button>
                        
                        <input type="number" x-model.number="quantity" min="1" max="{{ $product->stock_quantity }}" 
                               class="w-20 text-center py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                        
                        <button @click="if (quantity < maxStock) quantity++" 
                                type="button"
                                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white font-black text-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            +
                        </button>

                        <span class="text-xs text-slate-500 dark:text-slate-400 pl-2">Max: <strong class="text-slate-800 dark:text-slate-200">{{ $product->stock_quantity }}</strong></span>
                    </div>
                </div>

                <!-- Customer Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Your Name (Optional)</label>
                    <input type="text" x-model="customerName" placeholder="e.g. Ibrahim Bauchi" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <!-- Phone / Location -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Delivery Address / City (Optional)</label>
                    <input type="text" x-model="deliveryNote" placeholder="e.g. Wunti Market Area, Bauchi" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <!-- Summary Box -->
                <div class="p-4 bg-slate-50 dark:bg-slate-950/80 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Total Order Estimate</span>
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-400 font-display">
                            ₦<span x-text="totalEstimate"></span>
                        </span>
                    </div>
                    <span class="text-xs text-slate-500 font-medium" x-text="quantity + ' piece(s)'"></span>
                </div>

                <!-- Main WhatsApp CTA Button -->
                <a :href="whatsappLink" 
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-2xl shadow-lg shadow-emerald-600/20 transition-all transform active:scale-95 flex items-center justify-center space-x-2 text-center">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>Send Order Selection to WhatsApp &rarr;</span>
                </a>

                <p class="text-[11px] text-slate-500 text-center">
                    <i class="fa-solid fa-lock text-slate-400 mr-1"></i> Direct interaction with seller. Payment & pickup/delivery will be finalized directly on WhatsApp.
                </p>

            </div>

        </div>

    </div>

</div>
@endsection
