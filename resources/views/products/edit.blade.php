@extends('layouts.app')

@section('title', 'Edit ' . $product->name)
@section('page_title', 'Edit Product Details')
@section('page_subtitle', 'Update product specifications, variant size/volume, pricing & stock thresholds')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
        
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Product Specifications -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    1. Product Specifications & Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Product Name -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Product Photo Upload (Optional) -->
                    <div class="sm:col-span-2 space-y-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Photo (Optional, Max 1 Image)</label>
                        @if($product->image && file_exists(public_path($product->image)))
                            <div class="flex items-center space-x-3 p-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 w-fit">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 object-cover rounded-lg">
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Current custom photo</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-3 p-2 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-900/50 w-fit">
                                <img src="{{ asset('logo.png') }}" alt="Store Logo Default" class="h-10 w-auto object-contain">
                                <span class="text-xs text-amber-700 dark:text-amber-300 font-semibold">Default logo image</span>
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" 
                               class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-600 transition-all">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Size / Volume / Variant -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Size / Volume / Variant
                        </label>
                        <input type="text" name="size" value="{{ old('size', $product->size) }}" list="size-suggestions-edit"
                               placeholder="e.g. M, EU 42, 100ml, 40mm, One Size"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <datalist id="size-suggestions-edit">
                            <option value="S">Clothing: S</option>
                            <option value="M">Clothing: M</option>
                            <option value="L">Clothing: L</option>
                            <option value="XL">Clothing: XL</option>
                            <option value="XXL">Clothing: XXL</option>
                            <option value="EU 40">Shoes: EU 40</option>
                            <option value="EU 41">Shoes: EU 41</option>
                            <option value="EU 42">Shoes: EU 42</option>
                            <option value="EU 43">Shoes: EU 43</option>
                            <option value="EU 44">Shoes: EU 44</option>
                            <option value="50ml">Perfume: 50ml</option>
                            <option value="100ml">Perfume: 100ml</option>
                            <option value="200ml">Perfume: 200ml</option>
                            <option value="40mm">Watch: 40mm</option>
                            <option value="42mm">Watch: 42mm</option>
                            <option value="Standard">Standard / One Size</option>
                        </datalist>
                    </div>

                    <!-- Color / Finish / Scent -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Default Color / Finish / Scent</label>
                        <input type="text" name="color" value="{{ old('color', $product->color) }}" 
                               placeholder="e.g. Black, Gold, Woody Oud"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Available Sizes / Characteristics List -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Available Sizes / Characteristic Options (Comma Separated)
                        </label>
                        <input type="text" name="available_sizes" value="{{ old('available_sizes', $product->available_sizes) }}" 
                               placeholder="e.g. S, M, L, XL, XXL OR EU 40, EU 41, EU 42 OR 50ml, 100ml" 
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Enter multiple sizes separated by commas so storefront customers can select their preferred size.</p>
                    </div>

                    <!-- Available Colors / Options List -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Available Colors / Style Options (Comma Separated)
                        </label>
                        <input type="text" name="available_colors" value="{{ old('available_colors', $product->available_colors) }}" 
                               placeholder="e.g. Black, White, Navy Blue, Red, Olive Green" 
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Enter multiple colors separated by commas so storefront customers can choose their preferred color.</p>
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">SKU / Item Code <span class="text-rose-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" 
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Inventory Type & Units -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    2. Inventory Classification & Unit
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Usage Type Radio Group -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Inventory Stock Type <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                                <input type="radio" name="usage_type" value="retail" {{ old('usage_type', $product->usage_type) === 'retail' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-bag-shopping text-amber-500 mr-1"></i> Retail Stock</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                                <input type="radio" name="usage_type" value="display_sample" {{ old('usage_type', $product->usage_type) === 'display_sample' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-shirt text-amber-500 mr-1"></i> Display / Tester</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                                <input type="radio" name="usage_type" value="both" {{ old('usage_type', $product->usage_type) === 'both' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-rotate text-amber-500 mr-1"></i> Dual Usage</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Unit of Measurement -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Stock Unit of Measurement <span class="text-rose-500">*</span></label>
                        <select name="unit" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="piece" {{ old('unit', $product->unit) === 'piece' ? 'selected' : '' }}>piece (Clothes, Watches, Bags)</option>
                            <option value="pair" {{ old('unit', $product->unit) === 'pair' ? 'selected' : '' }}>pair (Shoes, Earrings)</option>
                            <option value="bottle" {{ old('unit', $product->unit) === 'bottle' ? 'selected' : '' }}>bottle (Perfumes, Oils)</option>
                            <option value="set" {{ old('unit', $product->unit) === 'set' ? 'selected' : '' }}>set (Jewelry Sets, Suits)</option>
                            <option value="pack" {{ old('unit', $product->unit) === 'pack' ? 'selected' : '' }}>pack (Accessories)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pricing & Alert Level -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    3. Pricing & Alert Threshold
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Cost Price -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Purchase Cost (₦) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Retail Selling Price (₦)</label>
                        <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Storefront Display Stock Count -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Storefront Display Stock</label>
                        <input type="number" min="0" name="display_stock_quantity" value="{{ old('display_stock_quantity', $product->display_stock_quantity) }}"
                               placeholder="e.g. 20 (optional)"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">Custom count shown to storefront visitors (e.g. 20).</p>
                    </div>

                    <!-- Minimum Stock Warning Level -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Min Stock Threshold <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description / Notes</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                @if(auth()->user()->isAdmin())
                    <button type="button" onclick="if(confirm('Delete this product record?')) document.getElementById('delete-product-form').submit();" class="text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold text-xs">
                        Delete Product
                    </button>
                @else
                    <div></div>
                @endif
                <div class="flex items-center space-x-3">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition-all">
                        Update Product Record
                    </button>
                </div>
            </div>

        </form>

        <form id="delete-product-form" action="{{ route('products.destroy', $product) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>
</div>
@endsection
