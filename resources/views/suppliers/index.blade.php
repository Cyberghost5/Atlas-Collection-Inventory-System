@extends('layouts.app')

@section('title', 'Suppliers')
@section('page_title', 'Manufacturers & Luxury Suppliers')
@section('page_subtitle', 'Manage clothing manufacturers, fragrance houses, horology vendors & leather suppliers')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 transition-colors">
        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Add New Supplier</h3>
        
        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Company / Brand Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Lagos Apparel Hub" 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Contact Person</label>
                <input type="text" name="contact_person" placeholder="e.g. Babatunde Adeleke" 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
                <input type="text" name="phone" placeholder="0802 111 2233" 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                <input type="email" name="email" placeholder="orders@lagosapparel.ng" 
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Physical Address</label>
                <textarea name="address" rows="2" placeholder="Victoria Island, Lagos, Nigeria..." 
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition-all"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow transition-all">
                Save Supplier
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 space-y-4 transition-colors">
        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Supplier Directory</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Supplier Name</th>
                        <th class="px-4 py-3">Contact Person</th>
                        <th class="px-4 py-3">Phone & Email</th>
                        <th class="px-4 py-3 text-center">Items Supplied</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                {{ $supplier->name }}
                                @if($supplier->address)
                                    <div class="text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ $supplier->address }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $supplier->contact_person ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                @if($supplier->phone) <div>📞 {{ $supplier->phone }}</div> @endif
                                @if($supplier->email) <div class="text-[10px] text-slate-400">✉️ {{ $supplier->email }}</div> @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400">
                                    {{ $supplier->products_count }} item(s)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                @if($supplier->phone)
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $supplier->phone);
                                        if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '234' . substr($cleanPhone, 1);
                                        }
                                        $msg = rawurlencode("Hello {$supplier->name}! Requesting order status update for Atlas Collection (Bauchi Store).");
                                        $waUrl = "https://wa.me/{$cleanPhone}?text={$msg}";
                                    @endphp
                                    <a href="{{ $waUrl }}" target="_blank" title="Chat with Supplier on WhatsApp" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] rounded-lg transition-all inline-flex items-center space-x-1">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                        </svg>
                                        <span>Chat</span>
                                    </a>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" onsubmit="return confirm('Delete supplier {{ $supplier->name }}?');" class="inline">
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
                            <td colspan="5" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                No suppliers registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
