<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          theme: localStorage.getItem('theme') || 'light',
          toggleTheme() {
              this.theme = (this.theme === 'light') ? 'dark' : 'light';
              localStorage.setItem('theme', this.theme);
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="
          if (theme === 'dark') {
              document.documentElement.classList.add('dark');
          } else {
              document.documentElement.classList.remove('dark');
          }
      "
      :class="{ 'dark': theme === 'dark' }"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Inventory System') | Atlas Collection</title>

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fffbe6',
                            100: '#fef3c7',
                            200: '#fde68a',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 transition-colors" 
      x-data="{ 
          mobileMenuOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
          }
      }">

    <div class="min-h-full flex flex-col md:flex-row w-full max-w-full overflow-x-hidden">
        <!-- Desktop Sidebar (Collapsible: w-64 vs w-20) -->
        <aside :class="sidebarCollapsed ? 'md:w-20' : 'md:w-64'" 
               class="hidden md:flex md:flex-col md:fixed md:inset-y-0 bg-slate-950 text-white z-30 shadow-xl border-r border-slate-800 transition-all duration-200">
            
            <!-- Brand Header -->
            <div class="flex items-center justify-between h-20 px-4 bg-slate-950 border-b border-slate-800">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group overflow-hidden">
                    <div class="p-1 bg-white rounded-xl shadow-md border border-amber-400/40 group-hover:scale-105 transition-transform flex-shrink-0">
                        <img src="{{ asset('logo.png') }}" alt="Atlas Collection Logo" class="h-9 w-auto object-contain">
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition class="overflow-hidden">
                        <h1 class="font-display font-extrabold text-sm tracking-wider text-white leading-tight">ATLAS</h1>
                        <p class="text-[9px] text-amber-400 font-black tracking-widest uppercase">COLLECTION</p>
                        <p class="text-[8px] text-slate-400 italic font-serif">...your style, our identity</p>
                    </div>
                </a>

                <!-- Collapse Toggle Button inside Sidebar -->
                <!-- <button @click="toggleSidebar()" 
                        type="button" 
                        title="Toggle Sidebar Width" 
                        class="p-1.5 rounded-lg bg-slate-900 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button> -->
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   title="Dashboard Overview"
                   class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Dashboard</span>
                </a>

                <!-- Inventory -->
                <a href="{{ route('products.index') }}" 
                   title="Inventory"
                   class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('products.index') || request()->routeIs('products.create') || request()->routeIs('products.edit') || request()->routeIs('products.show') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('products.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Inventory</span>
                </a>

                <!-- Orders -->
                <a href="{{ route('orders.index') }}" 
                   title="Orders & Sales"
                   class="flex items-center justify-between px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <div class="flex items-center min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('orders.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Orders</span>
                    </div>
                    @php $pendingCnt = \App\Models\Order::where('status', 'pending')->count(); @endphp
                    @if($pendingCnt > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950 border border-amber-300 flex-shrink-0">
                            {{ $pendingCnt }}
                        </span>
                    @endif
                </a>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}" 
                   title="Customers"
                   class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('customers.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Customers</span>
                </a>

                @if(auth()->check() && auth()->user()->isAdmin())
                    <!-- Transactions -->
                    <a href="{{ route('transactions.index') }}" 
                       title="Payment Transactions Ledger"
                       class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('transactions.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('transactions.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Transactions</span>
                    </a>

                    <!-- Stock Audit Log -->
                    <a href="{{ route('stock-movements.index') }}" 
                       title="Stock Audit Log"
                       class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('stock-movements.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('stock-movements.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Stock Audit Log</span>
                    </a>

                    <!-- Reports & Visual Analytics -->
                    <a href="{{ route('reports.index') }}" 
                       title="Executive Reports & Analytics"
                       class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('reports.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('reports.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Reports & Analytics</span>
                    </a>
                @endif

                <!-- Divider / Label -->
                <div class="pt-4 pb-1" x-show="!sidebarCollapsed">
                    <p class="px-3 text-[10px] font-bold text-amber-500/80 uppercase tracking-wider">Catalog Settings</p>
                </div>
                <div class="my-2 border-t border-slate-800" x-show="sidebarCollapsed"></div>

                <!-- Categories -->
                <a href="{{ route('categories.index') }}" 
                   title="Collection Categories"
                   class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('categories.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('categories.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h7m-7 4h7m-7 4h7M3 5h18a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Categories</span>
                </a>

                <!-- Suppliers -->
                <a href="{{ route('suppliers.index') }}" 
                   title="Suppliers"
                   class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('suppliers.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('suppliers.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4a2 2 0 012-2h2a2 2 0 012 2v4"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">Suppliers</span>
                </a>

                @if(auth()->check() && auth()->user()->isSuperAdmin())
                    <div class="pt-4 pb-1" x-show="!sidebarCollapsed">
                        <p class="px-3 text-[10px] font-bold text-amber-400 uppercase tracking-wider">Super Admin Module</p>
                    </div>
                    <div class="my-2 border-t border-slate-800" x-show="sidebarCollapsed"></div>

                    <!-- User Management -->
                    <a href="{{ route('users.index') }}" 
                       title="User & Role Management"
                       class="flex items-center px-3 py-3 text-sm font-semibold rounded-xl transition-all duration-150 {{ request()->routeIs('users.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-slate-950' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition class="ml-3 truncate">User Management</span>
                    </a>
                @endif
            </nav>

            <!-- User Info & Logout Footer -->
            <div class="p-3 bg-slate-950 border-t border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center font-black text-slate-950 text-xs shadow-md flex-shrink-0" title="{{ auth()->user()->name ?? 'Atlas Admin' }}">
                        {{ strtoupper(substr(auth()->user()->name ?? 'Atlas', 0, 2)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition class="overflow-hidden">
                        <p class="text-xs font-semibold text-slate-200 truncate">{{ auth()->user()->name ?? 'Atlas Admin' }}</p>
                        <p class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">{{ str_replace('_', ' ', auth()->user()->role ?? 'admin') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Header Bar -->
        <header class="md:hidden bg-slate-950 text-white flex items-center justify-between px-4 py-3 sticky top-0 z-40 shadow-md border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="p-1 bg-white rounded-lg">
                    <img src="{{ asset('logo.png') }}" alt="Atlas Collection Logo" class="h-7 w-auto">
                </div>
                <span class="font-display font-extrabold text-base text-white">ATLAS UNISEX</span>
            </div>
            
            <div class="flex items-center space-x-2">
                <!-- Light / Dark Mode Toggle Button (Mobile) -->
                <button @click="toggleTheme()" 
                        type="button"
                        title="Toggle Light / Dark Mode" 
                        class="p-2 rounded-xl border border-slate-800 bg-slate-900 text-amber-400 hover:bg-slate-800 transition-colors">
                    <template x-if="theme === 'dark'">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </template>
                    <template x-if="theme === 'light'">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </template>
                </button>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden bg-slate-900 text-white px-4 pt-3 pb-6 space-y-1.5 border-b border-slate-800 z-30 font-semibold text-sm">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <i class="fa-solid fa-chart-line text-amber-400"></i>
                <span>Dashboard</span>
            </a>

            <!-- Inventory -->
            <a href="{{ route('products.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('products.index') || request()->routeIs('products.create') || request()->routeIs('products.edit') || request()->routeIs('products.show') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <i class="fa-solid fa-boxes-stacked text-amber-400"></i>
                <span>Inventory</span>
            </a>

            <!-- Low Stock -->
            <a href="{{ route('products.low-stock') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('products.low-stock') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-rose-400 hover:bg-slate-800' }}">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <span>Low Stock Alerts</span>
                </div>
                @php $lowCnt = \App\Models\Product::where('stock_quantity', '<=', 5)->count(); @endphp
                @if($lowCnt > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">
                        {{ $lowCnt }}
                    </span>
                @endif
            </a>

            <!-- Orders & Sales -->
            <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-bag-shopping text-amber-400"></i>
                    <span>Orders & Sales</span>
                </div>
                @php $pendingCnt = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingCnt > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">
                        {{ $pendingCnt }}
                    </span>
                @endif
            </a>

            <!-- Record Counter Sale -->
            <a href="{{ route('orders.create') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('orders.create') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <i class="fa-solid fa-cart-plus text-amber-400"></i>
                <span>Record Counter Sale</span>
            </a>

            <!-- Customer Directory -->
            <a href="{{ route('customers.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('customers.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <i class="fa-solid fa-users text-amber-400"></i>
                <span>Customer Directory</span>
            </a>

            @if(auth()->check() && auth()->user()->isAdmin())
                <!-- Transactions Ledger -->
                <a href="{{ route('transactions.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('transactions.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-receipt text-amber-400"></i>
                    <span>Transactions Ledger</span>
                </a>

                <!-- Stock Audit Log -->
                <a href="{{ route('stock-movements.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('stock-movements.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-boxes-packing text-amber-400"></i>
                    <span>Stock Audit Log</span>
                </a>

                <!-- Executive Reports & Analytics -->
                <a href="{{ route('reports.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('reports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-chart-pie text-amber-400"></i>
                    <span>Reports & Analytics</span>
                </a>
            @endif

            <!-- Categories -->
            <a href="{{ route('categories.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('categories.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <i class="fa-solid fa-layer-group text-amber-400"></i>
                <span>Categories</span>
            </a>

            <!-- Suppliers -->
            <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('suppliers.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                <i class="fa-solid fa-truck-field text-amber-400"></i>
                <span>Suppliers</span>
            </a>

            @if(auth()->check() && auth()->user()->isSuperAdmin())
                <!-- User Management -->
                <a href="{{ route('users.index') }}" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('users.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="fa-solid fa-user-gear text-amber-400"></i>
                    <span>User Management</span>
                </a>
            @endif

            <!-- View Public Storefront -->
            <div class="pt-2 border-t border-slate-800">
                <a href="{{ route('shop.index') }}" target="_blank" class="flex items-center space-x-2.5 px-3 py-2.5 rounded-xl bg-slate-800 text-amber-400 hover:bg-slate-700 font-bold">
                    <i class="fa-solid fa-store text-amber-400"></i>
                    <span>View Storefront &rarr;</span>
                </a>
            </div>

        </div>

        <!-- Main Body (Padding Left dynamically expands from md:pl-64 to md:pl-20) -->
        <main :class="sidebarCollapsed ? 'md:pl-20' : 'md:pl-64'" 
              class="flex-1 flex flex-col min-h-screen min-w-0 w-full max-w-full overflow-x-hidden transition-all duration-200">

            @if(session()->has('impersonator_id'))
                <div class="bg-amber-500 text-slate-950 px-4 sm:px-6 py-2.5 text-xs font-bold flex items-center justify-between shadow-md z-30 sticky top-0 border-b border-amber-600">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-slate-950 text-amber-400 text-[10px] font-black uppercase tracking-wider">Impersonating</span>
                        <span>Logged in as <strong>{{ auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }})</span>
                    </div>
                    <form method="POST" action="{{ route('impersonate.stop') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-slate-950 hover:bg-slate-900 text-white rounded-lg text-[11px] font-extrabold shadow transition-all">
                            Exit & Return to Super Admin &rarr;
                        </button>
                    </form>
                </div>
            @endif

            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky {{ session()->has('impersonator_id') ? 'top-10' : 'top-0' }} z-20 px-4 sm:px-6 py-4 flex items-center justify-between shadow-sm transition-colors">
                <div class="flex items-center space-x-3">
                    <!-- Collapse Toggle Button (Top Navbar) -->
                    <button @click="toggleSidebar()" 
                            type="button"
                            title="Collapse / Expand Sidebar" 
                            class="hidden md:flex p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 transform transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white font-display">@yield('page_title', 'Dashboard')</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">@yield('page_subtitle', 'Atlas Collection Clothing Collection Inventory Tracking')</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    
                    <!-- Light / Dark Mode Toggle Button (Desktop Navbar) -->
                    <button @click="toggleTheme()" 
                            type="button"
                            title="Toggle Light / Dark Mode" 
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center space-x-1.5 text-xs font-bold">
                        <template x-if="theme === 'dark'">
                            <span class="flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="hidden sm:inline text-amber-400">Light</span>
                            </span>
                        </template>
                        <template x-if="theme === 'light'">
                            <span class="flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <span class="hidden sm:inline text-slate-700">Dark</span>
                            </span>
                        </template>
                    </button>

                    <a href="{{ route('shop.index') }}" target="_blank" class="hidden sm:inline-flex items-center px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid fa-store text-amber-500 mr-1.5"></i> Storefront
                    </a>
                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-3.5 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition-all border border-slate-700">
                        <svg class="w-4 h-4 mr-1 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Add Item</span>
                    </a>
                </div>
            </header>

            <!-- Alerts -->
            <div class="px-4 sm:px-6 pt-4">
                @if(session('success'))
                    <div class="p-4 mb-4 text-sm text-emerald-800 dark:text-emerald-300 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between shadow-sm" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 mb-4 text-sm text-rose-800 dark:text-rose-300 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 shadow-sm" role="alert">
                        <div class="flex items-center mb-1">
                            <svg class="w-5 h-5 mr-2 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold">Please fix validation errors:</span>
                        </div>
                        <ul class="list-disc list-inside pl-7 space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <div class="flex-1 px-4 sm:px-6 py-6 min-w-0 w-full max-w-full overflow-x-hidden">
                @yield('content')
            </div>

            <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 px-6 py-4 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
                <p>&copy; {{ date('Y') }} Atlas Collection Inventory System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
