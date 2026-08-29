@extends('layouts.app')

@section('title', 'Record Daily Sale / Create Order')
@section('page_title', 'Record Daily Sale / Order Entry')
@section('page_subtitle', 'Log counter sales or orders with instant inventory deduction')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" 
     x-data="{
         showConfirmModal: false,
         customerType: 'new',
         existingUserId: '',
         customerName: '{{ old('customer_name') }}',
         customerPhone: '{{ old('customer_phone') }}',
         customerEmail: '{{ old('customer_email') }}',
         shippingAddress: '{{ old('shipping_address', 'Store Pickup - Bauchi Main Branch') }}',
         paymentMethod: 'cash',
         orderStatus: 'completed',
         paymentStatus: 'paid',
         notes: '',
         existingCustomers: {{ json_encode($existingCustomers) }},

         items: [
             { product_id: '', searchTerm: '', quantity: 1, unit_price: 0, available_stock: 0 }
         ],
         products: {{ json_encode($products) }},

         onCustomerTypeChange() {
             if (this.customerType === 'new') {
                 this.existingUserId = '';
                 this.customerName = '';
                 this.customerPhone = '';
                 this.customerEmail = '';
                 this.shippingAddress = 'Store Pickup - Bauchi Main Branch';
             }
         },

         onExistingCustomerSelect() {
             let cust = this.existingCustomers.find(c => c.id == this.existingUserId);
             if (cust) {
                 this.customerName = cust.name || '';
                 this.customerPhone = cust.phone || '';
                 this.customerEmail = cust.email || '';
             }
         },

         addItem() {
             this.items.push({ product_id: '', searchTerm: '', quantity: 1, unit_price: 0, available_stock: 0 });
         },

         removeItem(index) {
             if (this.items.length > 1) {
                 this.items.splice(index, 1);
             }
         },

         getFilteredProducts(term) {
             if (!term || term.trim() === '') {
                 return this.products;
             }
             let q = term.toLowerCase().trim();
             return this.products.filter(p => {
                 return p.name.toLowerCase().includes(q) ||
                        (p.sku && p.sku.toLowerCase().includes(q)) ||
                        (p.size && p.size.toLowerCase().includes(q)) ||
                        (p.color && p.color.toLowerCase().includes(q));
             });
         },

         selectProduct(index, p) {
             let item = this.items[index];
             item.product_id = p.id;
             item.searchTerm = p.name + ' (Variant: ' + p.size + ')';
             item.unit_price = parseFloat(p.selling_price || p.cost_price);
             item.available_stock = parseInt(p.stock_quantity);
             if (item.quantity > item.available_stock) {
                 item.quantity = item.available_stock;
             }
         },

         get grandTotal() {
             return this.items.reduce((sum, item) => {
                 let q = parseInt(item.quantity) || 0;
                 let p = parseFloat(item.unit_price) || 0;
                 return sum + (q * p);
             }, 0);
         },

         openConfirmation() {
             if (!this.customerName.trim() || !this.customerPhone.trim() || !this.shippingAddress.trim()) {
                 alert('Please fill out all required customer details before proceeding.');
                 return;
             }
             let invalidItem = this.items.find(i => !i.product_id || i.quantity <= 0);
             if (invalidItem) {
                 alert('Please select a valid product item and quantity for all lines.');
                 return;
             }
             this.showConfirmModal = true;
         },

         submitForm() {
             this.$refs.orderForm.submit();
         }
     }">

    <div class="flex items-center justify-between">
        <a href="{{ route('orders.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center space-x-1">
            <span>&larr; Back to Orders</span>
        </a>
    </div>

    <form x-ref="orderForm" method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Customer & Delivery Information -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                1. Customer & Delivery Logistics
            </h3>

            <!-- Customer Type Radio Toggle -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center space-x-6">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Customer Category:</span>
                <label class="inline-flex items-center space-x-2 text-xs font-bold text-slate-800 dark:text-slate-200 cursor-pointer">
                    <input type="radio" name="customer_type" value="new" x-model="customerType" @change="onCustomerTypeChange()" class="text-amber-500 focus:ring-amber-500">
                    <span><i class="fa-solid fa-user-plus mr-1"></i> New Customer</span>
                </label>
                <label class="inline-flex items-center space-x-2 text-xs font-bold text-slate-800 dark:text-slate-200 cursor-pointer">
                    <input type="radio" name="customer_type" value="existing" x-model="customerType" @change="onCustomerTypeChange()" class="text-amber-500 focus:ring-amber-500">
                    <span><i class="fa-solid fa-users mr-1"></i> Existing Customer</span>
                </label>
            </div>

            <!-- If Existing Customer Selected -->
            <div x-show="customerType === 'existing'" class="space-y-3">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Select Existing Customer <span class="text-rose-500">*</span></label>
                <select name="existing_user_id" 
                        x-model="existingUserId" 
                        @change="onExistingCustomerSelect()" 
                        :required="customerType === 'existing'" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    <option value="">-- Choose Existing Customer --</option>
                    @foreach($existingCustomers as $cust)
                        <option value="{{ $cust->id }}">
                            {{ $cust->name }} ({{ $cust->phone ?? 'No Phone' }} - {{ $cust->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Customer Details Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" x-model="customerName" required 
                           placeholder="e.g. Ibrahim Abubakar" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_phone" x-model="customerPhone" required 
                           placeholder="0810 399 6947" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Email Address (Optional)</label>
                    <input type="email" name="customer_email" x-model="customerEmail" 
                           placeholder="customer@gmail.com" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Store Pickup / Delivery Address <span class="text-rose-500">*</span></label>
                    <input type="text" name="shipping_address" x-model="shippingAddress" required 
                           placeholder="e.g. Wunti Market Area, Bauchi" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Ordered Apparel Items Builder with Live Product Search -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                    2. Select Catalog Items & Quantities
                </h3>
                <button type="button" @click="addItem()" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                    <span>+ Add Line Item</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 items-end">
                        
                        <!-- Searchable Product Combobox -->
                        <div class="sm:col-span-5 relative" x-data="{ open: false }">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Search & Select Product (<span x-text="item.available_stock"></span> available)
                            </label>

                            <input type="text" 
                                   x-model="item.searchTerm" 
                                   @focus="open = true" 
                                   @input="open = true; item.product_id = ''" 
                                   placeholder="Type product name, size, SKU..." 
                                   required 
                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-amber-500 transition-all">

                            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id" required>

                            <!-- Live Search Results Dropdown -->
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition 
                                 class="absolute left-0 right-0 mt-1 max-h-52 overflow-y-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-50 divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                <template x-for="p in getFilteredProducts(item.searchTerm)" :key="p.id">
                                    <div @click="selectProduct(index, p); open = false;" 
                                         class="p-2.5 hover:bg-amber-50 dark:hover:bg-amber-950/50 cursor-pointer flex items-center justify-between transition-colors">
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block" x-text="p.name"></span>
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="'Variant: ' + p.size + ' • Color: ' + (p.color || 'Standard') + ' • SKU: ' + p.sku"></span>
                                        </div>
                                        <div class="text-right flex-shrink-0 ml-2">
                                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 block" x-text="'₦' + parseFloat(p.selling_price || p.cost_price).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                            <span class="text-[9px] font-extrabold" :class="p.stock_quantity > 0 ? 'text-slate-500 dark:text-slate-400' : 'text-rose-500'" x-text="p.stock_quantity + ' in stock'"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="getFilteredProducts(item.searchTerm).length === 0" class="p-3 text-center text-slate-400 text-xs">
                                    No matching catalog items found.
                                </div>
                            </div>
                        </div>

                        <!-- Unit Price (₦ NGN) -->
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Unit Price (₦ NGN)</label>
                            <input type="number" step="0.01" min="0" 
                                   :name="'items[' + index + '][unit_price]'" 
                                   x-model="item.unit_price" 
                                   required 
                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>

                        <!-- Quantity -->
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Qty</label>
                            <input type="number" min="1" :max="item.available_stock || 9999" 
                                   :name="'items[' + index + '][quantity]'" 
                                   x-model="item.quantity" 
                                   required 
                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>

                        <!-- Line Subtotal & Remove -->
                        <div class="sm:col-span-2 flex items-center justify-between space-x-2">
                            <div class="text-right">
                                <span class="block text-[9px] text-slate-400 uppercase font-semibold">Subtotal</span>
                                <span class="font-mono font-bold text-xs text-slate-900 dark:text-white" x-text="'₦' + ((item.quantity || 0) * (item.unit_price || 0)).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                            </div>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" title="Remove Item" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Total Amount Highlight -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs font-black text-slate-600 dark:text-slate-400 uppercase">Grand Total (NGN):</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-display" x-text="'₦' + grandTotal.toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
            </div>
        </div>

        <!-- Status, Payment Method & Conditional Proof Upload -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                3. Payment Method & Order Options
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Payment Method <span class="text-rose-500">*</span></label>
                    <select name="payment_method" x-model="paymentMethod" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <option value="cash">Cash Payment</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="pos">POS / Card Machine</option>
                        <option value="other">Other Payment</option>
                    </select>
                </div>

                <!-- Conditional Upload Payment Proof (Hidden for Cash and POS) -->
                <div x-show="!['cash', 'pos'].includes(paymentMethod)" x-transition>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Upload Payment Proof (Optional Receipt Image/PDF)</label>
                    <input type="file" name="payment_proof" accept="image/*,.pdf" 
                           class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-600 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Order Status <span class="text-rose-500">*</span></label>
                    <select name="status" x-model="orderStatus" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <option value="completed">Completed / Delivered</option>
                        <option value="processing">Processing / Packaging</option>
                        <option value="pending">Pending Review</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Payment Status <span class="text-rose-500">*</span></label>
                    <select name="payment_status" x-model="paymentStatus" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                        <option value="paid">Payment Received (Paid)</option>
                        <option value="unpaid">Pending Payment (Unpaid)</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sale Notes / Waybill Details (Optional)</label>
                    <input type="text" name="notes" x-model="notes" placeholder="e.g. Counter cash payment, Bauchi showroom pickup..." 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
            </div>

            <!-- Review & Confirm Order Trigger Button -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="button" @click="openConfirmation()" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-lg transition-all flex items-center space-x-2">
                    <span>Review & Confirm Order Entry &rarr;</span>
                </button>
            </div>
        </div>

    </form>

    <!-- Order Entry Confirmation Modal -->
    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" 
         style="display: none;">
        
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 text-xs text-slate-800 dark:text-slate-200">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="p-2 bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl font-bold text-sm"><i class="fa-solid fa-clipboard-list"></i></span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Confirm Sales Order Entry</h3>
                        <p class="text-[10px] text-slate-400">Please review order breakdown before deducting inventory</p>
                    </div>
                </div>
                <button @click="showConfirmModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg font-bold">&times;</button>
            </div>

            <!-- Customer Summary Card -->
            <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1.5">
                <div class="text-[10px] uppercase font-bold text-slate-400">Customer & Delivery Info</div>
                <div class="flex justify-between font-bold text-slate-900 dark:text-white">
                    <span x-text="customerName"></span>
                    <span class="font-mono text-amber-600 dark:text-amber-400" x-text="customerPhone"></span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate" x-text="shippingAddress"></div>
            </div>

            <!-- Items Table Preview -->
            <div class="space-y-2">
                <div class="text-[10px] uppercase font-bold text-slate-400">Order Line Items</div>
                <div class="max-h-40 overflow-y-auto border border-slate-200 dark:border-slate-800 rounded-2xl divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="(item, idx) in items" :key="idx">
                        <div class="p-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block" x-text="item.searchTerm || 'Item #' + (idx+1)"></span>
                                <span class="text-[10px] text-slate-400" x-text="'Qty: ' + item.quantity + ' × ₦' + parseFloat(item.unit_price || 0).toLocaleString('en-NG', {minimumFractionDigits:2})"></span>
                            </div>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" x-text="'₦' + ((item.quantity || 0) * (item.unit_price || 0)).toLocaleString('en-NG', {minimumFractionDigits:2})"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Payment & Order Status Badges -->
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                    <span class="text-[9px] uppercase text-slate-400 font-semibold block">Payment Method</span>
                    <span class="font-extrabold uppercase text-amber-600 dark:text-amber-400" x-text="paymentMethod.replace('_', ' ')"></span>
                </div>
                <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                    <span class="text-[9px] uppercase text-slate-400 font-semibold block">Payment Status</span>
                    <span class="font-extrabold uppercase text-emerald-600 dark:text-emerald-400" x-text="paymentStatus"></span>
                </div>
            </div>

            <!-- Total Highlight -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between font-black">
                <span class="text-slate-700 dark:text-slate-300">Total Order Amount:</span>
                <span class="text-xl text-emerald-600 dark:text-emerald-400 font-mono" x-text="'₦' + grandTotal.toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-2 flex items-center justify-end space-x-3">
                <button type="button" @click="showConfirmModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 transition-all">
                    Edit Details
                </button>
                <button type="button" @click="submitForm()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-1">
                    <span><i class="fa-solid fa-check mr-1"></i> Confirm & Save Order</span>
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
