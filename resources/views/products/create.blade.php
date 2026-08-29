@extends('layouts.app')

@section('title', 'Add New Product')
@section('page_title', 'Add Inventory Product')
@section('page_subtitle', 'Register clothes, perfumes, shoes, bags, watches, jewelry, or accessories to Atlas Collection')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
        
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Product Specifications -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    1. Product Specifications & Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Product Name -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               placeholder="e.g. Sauvage Oud Perfume, Leather Oxford Shoes, Heavyweight Hoodie, Gold Watch"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Product Image Upload (Optional) -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Photo (Optional, Max 1 Image)</label>
                        <input type="file" name="image" accept="image/*" 
                               class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-600 transition-all">
                        <p class="text-[10px] text-slate-400 mt-1">If left blank, the Atlas Collection logo will be used as the default display image on the storefront.</p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <input type="text" name="size" value="{{ old('size', 'Standard') }}" list="size-suggestions"
                               placeholder="e.g. M, EU 42, 100ml, 40mm, One Size"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <datalist id="size-suggestions">
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
                        <p class="text-[10px] text-slate-400 mt-1">Clothing: S/M/L • Shoes: EU 42 • Perfume: 100ml • Watch: 40mm</p>
                    </div>

                    <!-- Color / Finish / Scent -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Color / Finish / Scent Notes</label>
                        <input type="text" name="color" value="{{ old('color') }}" 
                               placeholder="e.g. Black, Gold, Woody Oud, Brown Leather"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Manufacturer / Supplier</label>
                        <select name="supplier_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">SKU / Item Code</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" 
                               placeholder="Auto-generated if empty (e.g. AUC-PRF-100-8941)"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Barcode (Optional)</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" 
                               placeholder="EAN/UPC Code"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Classification & Units -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    2. Inventory Stock Classification & Units
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Usage Type Radio Group -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Inventory Usage Type <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                                <input type="radio" name="usage_type" value="retail" {{ old('usage_type', 'retail') === 'retail' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-bag-shopping text-amber-500 mr-1"></i> Retail Stock</span>
                                    <span class="block text-[10px] text-slate-400">Standard sale items</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                                <input type="radio" name="usage_type" value="display_sample" {{ old('usage_type') === 'display_sample' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-shirt text-amber-500 mr-1"></i> Display / Tester</span>
                                    <span class="block text-[10px] text-slate-400">Showroom / Tester bottle</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                                <input type="radio" name="usage_type" value="both" {{ old('usage_type') === 'both' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-rotate text-amber-500 mr-1"></i> Dual Usage</span>
                                    <span class="block text-[10px] text-slate-400">Retail & Display</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Unit of Measurement -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Stock Unit of Measurement <span class="text-rose-500">*</span></label>
                        <select name="unit" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                            <option value="piece" {{ old('unit') === 'piece' ? 'selected' : '' }}>piece (Clothes, Watches, Bags)</option>
                            <option value="pair" {{ old('unit') === 'pair' ? 'selected' : '' }}>pair (Shoes, Earrings, Gloves)</option>
                            <option value="bottle" {{ old('unit') === 'bottle' ? 'selected' : '' }}>bottle (Perfumes, Oils, Mists)</option>
                            <option value="set" {{ old('unit') === 'set' ? 'selected' : '' }}>set (Jewelry Sets, Suits)</option>
                            <option value="pack" {{ old('unit') === 'pack' ? 'selected' : '' }}>pack (Accessories)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pricing & Stock Levels -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    3. Pricing & Stock Thresholds
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Cost Price -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Purchase / Cost Price (₦) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', '0.00') }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Retail Selling Price (₦)</label>
                        <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price') }}"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Initial Stock Quantity -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Initial Stock Quantity <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', '0') }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>

                    <!-- Minimum Stock Warning Level -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Min Stock Alert Level <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" name="min_stock_level" value="{{ old('min_stock_level', '5') }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Product Description / Notes</label>
                <textarea name="description" rows="3" placeholder="Fragrance notes, shoe material, watch case diameter, fit notes, care instructions..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">{{ old('description') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl hover:bg-slate-200 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition-all transform active:scale-95">
                    Save Product Record
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
