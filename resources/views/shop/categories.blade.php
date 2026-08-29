@extends('layouts.storefront')

@section('title', 'Product Categories | Atlas Collection Bauchi')
@section('meta_title', 'Browse Product Categories - Atlas Collection Bauchi')
@section('meta_description', 'Explore luxury unisex apparel, perfumes, footwear, bags, watches, jewelry, and fashion accessories by category at Atlas Collection in Bauchi, Nigeria.')
@section('meta_keywords', 'Atlas Collection Categories, Fashion Categories Bauchi, Perfumes Bauchi, Shoes Bauchi, Apparel Categories')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Header & Breadcrumb -->
    <div class="space-y-3">
        <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400">
            <a href="{{ route('shop.index') }}" class="hover:text-amber-500 transition-colors">Home</a>
            <span>&rarr;</span>
            <span class="text-amber-600 dark:text-amber-400 font-bold">Categories</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-display tracking-tight uppercase">
                    Product Categories
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Select a fashion category to explore available inventory items in our Bauchi store.
                </p>
            </div>
            <a href="{{ route('shop.index') }}" class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition-all inline-flex items-center space-x-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-layer-group text-amber-400"></i>
                <span>View Full Catalog</span>
            </a>
        </div>
    </div>

    <!-- Category Visual Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            @php
                $slugKey = strtolower($category->slug);
                $iconClass = $iconMap[$slugKey] ?? 'fa-bag-shopping';
            @endphp
            <a href="{{ route('shop.category.show', $category->slug) }}" 
               class="group bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-xl hover:border-amber-400 dark:hover:border-amber-500/50 transition-all duration-300 flex flex-col justify-between space-y-4">
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-slate-950 transition-all duration-300">
                            <i class="fa-solid {{ $iconClass }}"></i>
                        </div>
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400 font-extrabold text-xs rounded-full border border-slate-200 dark:border-slate-700">
                            {{ $category->products_count }} Item(s)
                        </span>
                    </div>

                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors font-display">
                            {{ $category->name }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed line-clamp-2">
                            {{ $category->description ?? 'Explore premium ' . strtolower($category->name) . ' items available for pickup or fast delivery in Nigeria.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-bold text-amber-600 dark:text-amber-400 group-hover:translate-x-1 transition-transform">
                    <span>Browse Collection</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-layer-group text-4xl text-slate-400 mb-3 block"></i>
                <p class="text-base font-bold text-slate-900 dark:text-white">No categories configured</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
