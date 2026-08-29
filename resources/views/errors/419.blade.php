@extends('errors.layout')

@section('title', '419 - Session Expired | Atlas Collection Bauchi')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6">
    
    <!-- Stylized 419 Badge & Icon -->
    <div class="space-y-2">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-4xl mb-2 border border-amber-500/20 shadow-inner">
            <i class="fa-solid fa-hourglass-half animate-pulse"></i>
        </div>
        <div class="font-display font-black text-6xl sm:text-7xl text-amber-500 tracking-tight leading-none">
            419
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white uppercase font-display">
            Form Session Expired
        </h1>
    </div>

    <!-- Description -->
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto">
        Your security token or form session expired due to a period of inactivity. Please refresh the page and resubmit your form.
    </p>

    <!-- Action Buttons -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-center gap-3 text-xs font-bold">
        <button onclick="window.location.reload()" type="button" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl shadow transition-all flex items-center space-x-2 font-extrabold">
            <i class="fa-solid fa-rotate-right"></i>
            <span>Refresh & Retry</span>
        </button>
        <a href="{{ route('shop.index') }}" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white rounded-xl shadow transition-all flex items-center space-x-2">
            <i class="fa-solid fa-house"></i>
            <span>Return to Home</span>
        </a>
    </div>

</div>
@endsection
