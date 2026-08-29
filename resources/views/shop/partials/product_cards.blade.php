@foreach($products as $product)
    @php
        $itemUrl = route('shop.show', $product->slug ?? $product->id);
    @endphp
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl hover:border-amber-400/60 transition-all group flex flex-col justify-between">
        <a href="{{ $itemUrl }}">
        
            <!-- Product Picture Presentation -->
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-950 overflow-hidden">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                
                <!-- Size Badge -->
                <div class="absolute top-3 left-3 bg-slate-900/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-xs font-black text-white border border-slate-700">
                    Size: {{ $product->size }}
                </div>

                <!-- Copy Link Action -->
                <button @click="copyItemLink('{{ $itemUrl }}')" 
                        type="button"
                        title="Copy Direct Item Link"
                        class="absolute top-3 right-3 p-2 rounded-xl bg-slate-900/80 backdrop-blur-md text-slate-200 hover:text-amber-400 hover:bg-slate-900 border border-slate-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>

                <!-- Stock Status Overlay -->
                <div class="absolute bottom-3 left-3">
                    @if($product->stock_quantity > 0)
                        <span class="bg-emerald-900/90 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-emerald-300 border border-emerald-700">
                            Available: {{ $product->stock_quantity }} {{ $product->unit }}(s)
                        </span>
                    @else
                        <span class="bg-rose-900/90 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-rose-300 border border-rose-700">
                            Out of Stock
                        </span>
                    @endif
                </div>
            </div>

            <!-- Product Details Body -->
            <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-[10px] uppercase font-bold text-amber-600 dark:text-amber-400 tracking-wider">
                        <span>{{ $product->category->name ?? 'Streetwear' }}</span>
                        @if($product->color)
                            <span class="text-slate-500 dark:text-slate-400 font-normal"><i class="fa-solid fa-palette text-amber-500 mr-1"></i> {{ $product->color }}</span>
                        @endif
                    </div>

                    <h3 class="font-bold text-slate-900 dark:text-white text-base mt-1 line-clamp-1 group-hover:text-amber-500 transition-colors">
                        <a href="{{ $itemUrl }}">{{ $product->name }}</a>
                    </h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1">
                        {{ $product->description ?? 'Premium heavyweight cotton unisex garment.' }}
                    </p>
                </div>

                <!-- Price & Quick WhatsApp Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-baseline justify-between">
                        <span class="text-[10px] uppercase text-slate-400 font-semibold">Price</span>
                        <span class="text-lg font-black text-amber-600 dark:text-amber-400 font-display">
                            ₦{{ number_format($product->selling_price ?? $product->cost_price, 2) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ $itemUrl }}" 
                        class="py-2.5 px-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-[11px] rounded-xl text-center transition-all">
                            Details
                        </a>

                        <button @click="openQuickModal({{ json_encode($product) }})" 
                                type="button"
                                class="py-2.5 px-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] rounded-xl shadow-md text-center transition-all flex items-center justify-center space-x-1">
                            <i class="fa-brands fa-whatsapp mr-1 text-sm"></i>
                            <span>Quick Order</span>
                        </button>
                    </div>
                </div>
            </div>

        </a>
    </div>
@endforeach
