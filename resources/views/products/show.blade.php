@extends('layouts.app')

@section('title', $product->name)
@section('page_title', $product->name)
@section('page_subtitle', 'Apparel specifications, size/color variant details & stock movement logger')

@section('content')
<div class="space-y-6">

    <!-- Top Card: Summary & Quick Actions -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-6 transition-colors">
        
        <!-- Left: Details -->
        <div class="md:col-span-2 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-xl font-bold font-display text-slate-900 dark:text-white">{{ $product->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded text-xs font-black bg-slate-900 dark:bg-slate-800 text-white dark:text-amber-400 border border-slate-700">
                        Size: {{ $product->size }}
                    </span>
                    @if($product->is_low_stock)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                            Low Stock
                        </span>
                    @endif
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('products.barcode', $product) }}" target="_blank" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                        <span><i class="fa-solid fa-barcode mr-1"></i> Print Price Tag</span>
                    </a>
                    <a href="{{ route('products.edit', $product) }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Edit Item</a>
                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product from collection inventory?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 font-semibold text-xs rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-all">Delete</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 py-2">
                <div>
                    <span class="block text-[10px] uppercase font-semibold text-slate-400">SKU</span>
                    <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">{{ $product->sku }}</span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-semibold text-slate-400">Colorway</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $product->color ?? 'Standard' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-semibold text-slate-400">Category</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-semibold text-slate-400">Classification</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center">
                        @if($product->usage_type === 'display_sample') <i class="fa-solid fa-shirt text-amber-500 mr-1"></i> Display
                        @elseif($product->usage_type === 'retail') <i class="fa-solid fa-bag-shopping text-emerald-500 mr-1"></i> Retail Stock
                        @else <i class="fa-solid fa-rotate text-blue-500 mr-1"></i> Dual Usage @endif
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-semibold text-slate-400">Production Cost</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">₦{{ number_format($product->cost_price, 2) }}</span>
                </div>
                <div>
                    <span class="block text-[10px] uppercase font-semibold text-slate-400">Retail Price</span>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        {{ $product->selling_price ? '₦' . number_format($product->selling_price, 2) : 'N/A' }}
                    </span>
                </div>
            </div>

            @if($product->description)
                <p class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                    {{ $product->description }}
                </p>
            @endif
        </div>

        <!-- Right: Current Stock Level Highlight -->
        <div class="bg-slate-900 text-white rounded-xl p-5 flex flex-col justify-between border border-slate-800">
            <div>
                <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider">Inventory Count</span>
                <div class="text-4xl font-extrabold font-display mt-1 text-indigo-400">
                    {{ $product->stock_quantity }} <span class="text-base font-normal text-slate-300">{{ $product->unit }}(s)</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Alert Threshold: <span class="font-bold text-slate-200">{{ $product->min_stock_level }} {{ $product->unit }}</span></p>
            </div>
            <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-400">Total Stock Value:</span>
                <span class="font-bold text-emerald-400">₦{{ number_format($product->stock_quantity * $product->cost_price, 2) }}</span>
            </div>
        </div>

    </div>

    <!-- Quick Stock Movement Logging Form -->
    <div x-data="{
            type: 'in',
            quantity: 1,
            notes: '',
            currentStock: {{ $product->stock_quantity }},
            showMovementConfirmModal: false,

            get typeLabel() {
                switch(this.type) {
                    case 'in': return 'Restock (+)';
                    case 'out_internal': return 'Showroom Allocation (-)';
                    case 'out_sale': return 'Retail Sale (-)';
                    case 'adjustment': return 'Inventory Recount / Adjustment (=)';
                    default: return 'Movement';
                }
            },

            get newStock() {
                let qty = parseInt(this.quantity) || 0;
                if (this.type === 'in') return this.currentStock + qty;
                if (['out_internal', 'out_sale'].includes(this.type)) return Math.max(0, this.currentStock - qty);
                if (this.type === 'adjustment') return qty;
                return this.currentStock;
            },

            submitForm() {
                document.getElementById('stock-movement-form').submit();
            }
        }">

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors">
            <h3 class="text-base font-bold font-display text-slate-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Log Stock Movement / Transaction
            </h3>

            <form id="stock-movement-form" method="POST" action="{{ route('products.stock-movement.store', $product) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                @csrf

                <!-- Movement Type -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Transaction Type</label>
                    <select name="type" x-model="type" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="in">Restock (+)</option>
                        <option value="out_internal">Display (-)</option>
                        <option value="out_sale">Order Sale (-)</option>
                        <option value="adjustment">Inventory Recount / Adjustment (=)</option>
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Quantity ({{ $product->unit }})</label>
                    <input type="number" name="quantity" x-model.number="quantity" min="1" value="1" required 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Notes / Order Ref (Optional)</label>
                    <input type="text" name="notes" x-model="notes" placeholder="e.g. Order #1042, Showroom fitting display..." 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Submit Button (Triggers Confirmation Modal) -->
                <div class="flex items-end">
                    <button type="button" @click="showMovementConfirmModal = true" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-boxes-packing"></i>
                        <span>Log Movement Entry</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Stock Movement Confirmation Modal -->
        <div x-show="showMovementConfirmModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.away="showMovementConfirmModal = false" 
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-xs text-slate-800 dark:text-slate-200">
                
                <div class="flex items-center space-x-3 text-indigo-600 dark:text-indigo-400">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-xl font-bold border border-indigo-500/20">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Confirm Stock Movement Entry</h3>
                        <p class="text-[10px] text-slate-400">Audit trail ledger confirmation</p>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-950/70 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <span class="text-slate-400">Item Name:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $product->name }} ({{ $product->sku }})</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <span class="text-slate-400">Movement Type:</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="typeLabel"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <span class="text-slate-400">Quantity Change:</span>
                        <span class="font-mono font-extrabold text-slate-900 dark:text-white" x-text="quantity + ' {{ $product->unit }}(s)'"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <span class="text-slate-400">Current Stock:</span>
                        <span class="font-mono text-slate-600 dark:text-slate-300" x-text="currentStock + ' {{ $product->unit }}(s)'"></span>
                    </div>
                    <div class="flex justify-between pt-1 font-bold">
                        <span class="text-slate-400">New Stock Level:</span>
                        <span class="font-mono text-emerald-600 dark:text-emerald-400 text-sm" x-text="newStock + ' {{ $product->unit }}(s)'"></span>
                    </div>
                    <template x-if="notes.trim() !== ''">
                        <div class="pt-2 text-[11px] text-slate-500 border-t border-slate-200 dark:border-slate-800">
                            <span class="font-semibold text-slate-400">Notes:</span> <span x-text="notes"></span>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showMovementConfirmModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-200 transition-all">
                        Cancel
                    </button>
                    <button type="button" @click="submitForm()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-xl shadow-lg transition-all flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Confirm & Commit Stock Entry</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Movement History -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 space-y-4 transition-colors w-full max-w-full">
        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Stock Movement History Audit Log</h3>

        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[700px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-center">Quantity</th>
                        <th class="px-4 py-3">Notes</th>
                        <th class="px-4 py-3">Logged By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($product->stockMovements()->latest()->get() as $movement)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-slate-500 dark:text-slate-400 text-[11px]">
                                {{ $movement->created_at->format('M d, Y - h:i A') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($movement->type === 'in')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Restock (+)</span>
                                @elseif($movement->type === 'out_internal')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Display Sample (-)</span>
                                @elseif($movement->type === 'out_sale')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Retail Sale (-)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Adjusted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-slate-900 dark:text-white">
                                {{ $movement->quantity }} {{ $product->unit }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ $movement->notes ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                                {{ $movement->user->name ?? 'Atlas Admin' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                No stock movement history recorded for this apparel item yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
