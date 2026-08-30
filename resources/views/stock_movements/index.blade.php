@extends('layouts.app')

@section('title', 'Stock Audit Ledger')
@section('page_title', 'Stock Movement Audit Trail')
@section('page_subtitle', 'Complete historical ledger of restocks, display movement, retail sales & audit adjustments')

@section('content')
<div class="space-y-6">

    <!-- Per Page Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <span class="text-xs font-bold text-slate-700 dark:text-slate-300"><i class="fa-solid fa-clock-rotate-left mr-1.5 text-amber-500"></i> Stock Audit Log History</span>
        <form method="GET" action="{{ route('stock-movements.index') }}" class="flex items-center space-x-2">
            <span class="text-xs text-slate-500 font-medium">Rows per page:</span>
            <select name="per_page" onchange="this.form.submit()" class="py-1.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 Logs</option>
                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 Logs</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Logs</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Logs</option>
                <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 Logs</option>
            </select>
        </form>
    </div>

    <!-- Table of Stock Movements -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden w-full max-w-full">
        <div class="overflow-x-auto w-full max-w-full">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 min-w-[800px]">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Timestamp</th>
                        <th class="px-6 py-4 min-w-[200px]">Product Info</th>
                        <th class="px-6 py-4 whitespace-nowrap">Category</th>
                        <th class="px-6 py-4 whitespace-nowrap">Movement Type</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Quantity</th>
                        <th class="px-6 py-4 min-w-[180px]">Notes / Details</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Logged By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-slate-500 dark:text-slate-400 text-[11px] whitespace-nowrap">
                                {{ $movement->created_at->format('Y-m-d h:i A') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                @if($movement->product)
                                    <a href="{{ route('products.show', $movement->product) }}" class="hover:text-amber-500 transition-colors">
                                        {{ $movement->product->name }}
                                    </a>
                                @else
                                    <span class="text-slate-400">Deleted Product</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $movement->product->category->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($movement->type === 'in')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <i class="fa-solid fa-boxes-packing mr-1"></i> Restock (+)
                                    </span>
                                @elseif($movement->type === 'out_internal')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        <i class="fa-solid fa-shirt mr-1"></i> Showroom (-)
                                    </span>
                                @elseif($movement->type === 'out_sale')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <i class="fa-solid fa-bag-shopping mr-1"></i> Retail Sale (-)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Audit Adjustment
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-900 dark:text-white font-mono">
                                {{ $movement->quantity }} {{ $movement->product->unit ?? 'unit' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                {{ $movement->notes ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500 dark:text-slate-400 font-medium">
                                {{ $movement->user->name ?? 'System Admin' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                No stock movement records logged yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
