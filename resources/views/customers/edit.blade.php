@extends('layouts.app')

@section('title', 'Edit Customer - ' . $customer->name)
@section('page_title', 'Edit Customer Profile')
@section('page_subtitle', 'Update contact information and delivery address')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('customers.show', $customer->phone ?? $customer->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center space-x-1">
            <span>&larr; Cancel & Return to Customer Profile</span>
        </a>
    </div>

    <!-- Edit Customer Form Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6 transition-colors">
        <div class="flex items-center space-x-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-black flex items-center justify-center text-sm shadow">
                {{ strtoupper(substr($customer->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit {{ $customer->name }}</h3>
                <p class="text-xs text-slate-400">Update customer details across sales ledgers & VIP profile</p>
            </div>
        </div>

        <form method="POST" action="{{ route('customers.update', $customer->phone ?? $customer->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Customer Full Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Full Customer Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required 
                           placeholder="e.g. Alhaji Mustapha Bauchi" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    @error('name')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Phone -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Contact Phone Number <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required 
                           placeholder="e.g. 08103996947" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                    @error('phone')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Email Address <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" 
                       placeholder="e.g. customer@example.com" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
                @error('email')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Shipping / Delivery Address -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Shipping & Delivery Address <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <textarea name="address" rows="3" placeholder="e.g. Shop 12, Wunti Market, Bauchi, Bauchi State..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">{{ old('address', $customer->address) }}</textarea>
                @error('address')
                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Action Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('customers.show', $customer->phone ?? $customer->id) }}" 
                   class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-md transition-all flex items-center space-x-1">
                    <span><i class="fa-solid fa-floppy-disk mr-1"></i> Save Customer Profile</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
