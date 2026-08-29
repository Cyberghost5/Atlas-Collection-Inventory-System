@extends('errors.layout')

@section('title', '404 - Page Not Found | Atlas Collection Bauchi')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
    
    <!-- Stylized 404 Badge & Icon -->
    <div class="space-y-2">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-4xl mb-2 border border-amber-500/20 shadow-inner">
            <i class="fa-solid fa-compass animate-spin-slow"></i>
        </div>
        <div class="font-display font-black text-6xl sm:text-7xl text-amber-500 tracking-tight leading-none">
            404
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white uppercase font-display">
            Page or Catalog Item Not Found
        </h1>
    </div>

    <!-- Description -->
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto">
        The apparel item, category page, or link you are looking for doesn't exist, has been renamed, or was removed from our Bauchi catalog.
    </p>

    <!-- Search Form -->
    <form method="GET" action="{{ route('shop.index') }}" class="max-w-md mx-auto">
        <div class="relative flex items-center">
            <input type="text" name="search" placeholder="Search product name, size, SKU..." required
                   class="w-full pl-10 pr-24 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </div>
            <button type="submit" class="absolute right-1.5 px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow transition-all">
                Search
            </button>
        </div>
    </form>

    <!-- Action Buttons -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-center gap-3 text-xs font-bold">
        <a href="{{ route('shop.index') }}" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white rounded-xl shadow transition-all flex items-center space-x-2">
            <i class="fa-solid fa-house"></i>
            <span>Return to Home</span>
        </a>
        <a href="{{ route('shop.categories') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 rounded-xl transition-all flex items-center space-x-2 border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-layer-group text-amber-500"></i>
            <span>Browse Categories</span>
        </a>
        <a href="https://wa.me/2348103996947?text={{ rawurlencode('Hello Atlas Collection! I am looking for an item on your storefront but encountered a 404 error.') }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl shadow transition-all flex items-center space-x-2">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>WhatsApp Support</span>
        </a>
    </div>

</div>
@endsection
