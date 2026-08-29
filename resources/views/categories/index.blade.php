@extends('layouts.app')

@section('title', 'Collection Categories')
@section('page_title', 'Catalog Categories')
@section('page_subtitle', 'Organize clothes, perfumes, shoes, bags, watches, jewelry & accessories')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Add Catalog Category</h3>
        
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Category Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Perfumes & Fragrances" 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description (Optional)</label>
                <textarea name="description" rows="3" placeholder="Category notes..." 
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all">
                Save Category
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 space-y-4 transition-colors">
        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Catalog Categories</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Category Name</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3 text-center">Items Count</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                {{ $category->name }}
                                @if($category->description)
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $category->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-500 dark:text-slate-400 text-[11px]">
                                {{ $category->slug }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400">
                                    {{ $category->products_count }} item(s)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Delete category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-700 font-semibold text-[11px]">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                No categories created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
