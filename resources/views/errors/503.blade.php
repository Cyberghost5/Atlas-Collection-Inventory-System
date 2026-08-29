@extends('errors.layout')

@section('title', '503 - Store Maintenance | Atlas Collection Bauchi')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
    
    <!-- Stylized 503 Badge & Icon -->
    <div class="space-y-2">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-4xl mb-2 border border-amber-500/20 shadow-inner">
            <i class="fa-solid fa-screwdriver-wrench animate-bounce"></i>
        </div>
        <div class="font-display font-black text-6xl sm:text-7xl text-amber-500 tracking-tight leading-none">
            503
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white uppercase font-display">
            Store Under Maintenance
        </h1>
    </div>

    <!-- Description -->
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto">
        Atlas Collection is currently undergoing scheduled system updates to improve your shopping experience. You can still reach us directly on WhatsApp for order inquiries.
    </p>

    <!-- Direct Contact Box -->
    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs space-y-1.5 max-w-md mx-auto text-left">
        <div class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] mb-1">Bauchi Store Direct Counter:</div>
        <p><i class="fa-solid fa-location-dot text-amber-500 mr-1.5"></i> Wunti market, Bababa plaza, shop E7 Block E, Bauchi</p>
        <p><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i> Phone: 0810 399 6947</p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-center gap-3 text-xs font-bold">
        <button onclick="window.location.reload()" type="button" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl shadow transition-all flex items-center space-x-2 font-extrabold">
            <i class="fa-solid fa-rotate-right"></i>
            <span>Check Status</span>
        </button>
        <a href="https://wa.me/2348103996947?text={{ rawurlencode('Hello Atlas Collection! I would like to make an inquiry while the site is under maintenance.') }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl shadow transition-all flex items-center space-x-2">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>Order via WhatsApp</span>
        </a>
    </div>

</div>
@endsection
