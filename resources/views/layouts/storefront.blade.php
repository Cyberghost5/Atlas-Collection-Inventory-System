<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      class="h-full" 
      x-data="{ 
          mobileMenuOpen: false,
          theme: localStorage.getItem('theme') || 'light', 
          toggleTheme() { 
              this.theme = this.theme === 'dark' ? 'light' : 'dark'; 
              localStorage.setItem('theme', this.theme); 
              document.documentElement.classList.toggle('dark', this.theme === 'dark'); 
          } 
      }" 
      x-init="document.documentElement.classList.toggle('dark', theme === 'dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Primary SEO Meta Tags -->
    <title>@yield('title', 'Atlas Collection - Premium Clothes, Perfumes, Shoes, Bags, Watches & Jewelry in Bauchi')</title>
    <meta name="description" content="@yield('meta_description', 'Atlas Collection in Bauchi, Nigeria. Shop luxury unisex clothes, designer perfumes, shoes, bags, watches, and jewelry. Located at Wunti Market, Bababa Plaza, Shop E7 Block E. Order directly via WhatsApp: 0810 399 6947.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Atlas Collection, Atlas Collection Bauchi, clothes Bauchi, perfumes Bauchi, footwear Bauchi, bags Bauchi, watches Bauchi, jewelry Bauchi, luxury unisex fashion Nigeria, Wunti market fashion store')">
    <meta name="author" content="Atlas Collection">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon & Web App Theme Color -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <meta name="theme-color" content="#d97706">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Open Graph / Facebook / WhatsApp Preview Meta Tags -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Atlas Collection Bauchi">
    <meta property="og:title" content="@yield('meta_title', 'Atlas Collection - Premium Clothes, Perfumes, Shoes, Bags, Watches & Jewelry in Bauchi')">
    <meta property="og:description" content="@yield('meta_description', 'Atlas Collection in Bauchi, Nigeria. Shop luxury unisex clothes, designer perfumes, shoes, bags, watches, and jewelry. Located at Wunti Market, Bababa Plaza, Shop E7 Block E. Order directly via WhatsApp: 0810 399 6947.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('meta_image', asset('logo.png'))">
    <meta property="og:locale" content="en_NG">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', 'Atlas Collection - Premium Clothes, Perfumes, Shoes, Bags, Watches & Jewelry in Bauchi')">
    <meta name="twitter:description" content="@yield('meta_description', 'Atlas Collection in Bauchi, Nigeria. Shop luxury unisex clothes, designer perfumes, shoes, bags, watches, and jewelry.')">
    <meta name="twitter:image" content="@yield('meta_image', asset('logo.png'))">

    <!-- Schema.org JSON-LD Structured Data for ClothingStore / LocalBusiness -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ClothingStore",
      "name": "Atlas Collection",
      "image": "{{ asset('logo.png') }}",
      "@id": "{{ url('/') }}",
      "url": "{{ url('/') }}",
      "telephone": "{{ config('services.store.phone', '0810 399 6947') }}",
      "email": "{{ config('services.store.email', 'atlascollection6@gmail.com') }}",
      "priceRange": "₦₦",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Wunti market, Bababa plaza, shop E7 Block E (Beside New Flyover)",
        "addressLocality": "Bauchi",
        "addressRegion": "Bauchi State",
        "postalCode": "740242",
        "addressCountry": "NG"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 10.3158,
        "longitude": 9.8442
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
        ],
        "opens": "08:00",
        "closes": "20:00"
      },
      "sameAs": [
        "{{ config('services.social.instagram') }}",
        "{{ config('services.social.facebook') }}",
        "{{ config('services.social.tiktok') }}"
      ]
    }
    </script>
    @stack('schema')

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
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
                            500: '#f59e0b', // Logo Warm Amber
                            600: '#d97706',
                            700: '#b45309',
                            900: '#78350f',
                        },
                        accent: {
                            500: '#f97316', // Logo Orange Accent
                            600: '#ea580c',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-200">

    <!-- Top Announcement & Social Bar -->
    <div class="bg-gradient-to-r from-amber-600 via-amber-700 to-amber-600 dark:from-amber-950 dark:via-slate-950 dark:to-amber-950 text-white dark:text-amber-200 py-2 px-4 text-center text-[11px] font-medium tracking-wide flex items-center justify-between border-b border-amber-500/20 shadow-sm">
        <div class="max-w-7xl mx-auto w-full flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center space-x-2 truncate">
                <span><i class="fa-solid fa-location-dot text-amber-300 mr-1"></i> Wunti Market, Bababa Plaza, Block E, Shop E7 (Beside New Flyover), Bauchi, Nigeria</span>
                <span class="hidden md:inline-block font-extrabold text-amber-200 dark:text-amber-400">| ...your style, our identity</span>
            </div>
            
            <!-- Social Media Links (Header Bar) -->
            <div class="flex items-center space-x-3 flex-shrink-0">
                <span class="text-[10px] uppercase font-bold text-amber-100 dark:text-amber-400/80 tracking-wider">Follow Us:</span>
                
                <!-- Instagram -->
                <a href="{{ config('services.social.instagram') }}" target="_blank" rel="noopener noreferrer" 
                   title="Follow us on Instagram" 
                   class="p-1 rounded-lg hover:bg-amber-500/20 text-white dark:text-amber-300 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                
                <!-- Facebook -->
                <a href="{{ config('services.social.facebook') }}" target="_blank" rel="noopener noreferrer" 
                   title="Follow us on Facebook" 
                   class="p-1 rounded-lg hover:bg-amber-500/20 text-white dark:text-amber-300 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                <!-- TikTok -->
                <a href="{{ config('services.social.tiktok') }}" target="_blank" rel="noopener noreferrer" 
                   title="Follow us on TikTok" 
                   class="p-1 rounded-lg hover:bg-amber-500/20 text-white dark:text-amber-300 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.82.57-1.33 1.51-1.42 2.51-.12 1.18.34 2.38 1.22 3.16.89.78 2.14 1.06 3.29.74 1.12-.3 2.05-1.18 2.38-2.28.18-.58.23-1.2.22-1.81V.02z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white/90 dark:bg-slate-950/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 sm:h-24">
                
                <!-- Brand Logo & Motto -->
                <a href="{{ route('shop.index') }}" class="flex items-center space-x-3.5 group py-2">
                    <div class="p-1.5 bg-white rounded-2xl shadow-md border border-amber-400/50 group-hover:scale-105 transition-transform">
                        <img src="{{ asset('logo.png') }}" alt="Atlas Collection Logo" class="h-10 sm:h-12 w-auto object-contain">
                    </div>
                    <div>
                        <span class="font-display font-black text-xl sm:text-2xl tracking-wider text-slate-900 dark:text-white group-hover:text-amber-500 transition-colors">ATLAS</span>
                        <span class="block text-[10px] uppercase font-extrabold text-amber-600 dark:text-amber-400 tracking-widest -mt-0.5">COLLECTION</span>
                        <span class="block text-[9px] italic text-slate-500 dark:text-slate-400 tracking-wide font-serif">...your style, our identity</span>
                    </div>
                </a>

                <!-- Desktop Header Navigation Links -->
                <div class="hidden md:flex items-center space-x-4">
                    
                    <!-- Currency Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30">
                        (₦ NGN)
                    </span>

                    <!-- Home Link -->
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-amber-500 dark:hover:text-amber-400 transition-all space-x-1.5">
                        <i class="fa-solid fa-house text-amber-500"></i>
                        <span>Home</span>
                    </a>

                    <!-- Store Categories Link -->
                    <a href="{{ route('shop.categories') }}" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 hover:border-amber-500 dark:hover:border-amber-500 transition-all space-x-1.5 shadow-sm">
                        <i class="fa-solid fa-layer-group text-amber-500"></i>
                        <span>Categories</span>
                    </a>

                    <!-- Track Orders Link -->
                    <a href="{{ route('shop.my-orders') }}" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-amber-500 dark:hover:text-amber-400 transition-all space-x-1.5">
                        <i class="fa-solid fa-box-archive text-amber-500"></i>
                        <span>My Orders</span>
                    </a>

                    <!-- Light / Dark Mode Toggle Button -->
                    <button @click="toggleTheme()" 
                            type="button"
                            title="Toggle Light / Dark Mode" 
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-amber-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors flex items-center space-x-1.5 text-xs font-bold">
                        <template x-if="theme === 'dark'">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Light</span>
                            </span>
                        </template>
                        <template x-if="theme === 'light'">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <span>Dark</span>
                            </span>
                        </template>
                    </button>

                    <!-- Staff/Admin Control Link -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition-all">
                            Admin Dashboard &rarr;
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-rose-500 transition-colors">
                                Logout
                            </button>
                        </form>
                    @endauth

                </div>

                <!-- Mobile Header Right Actions (Categories shortcut + Hamburger Menu Toggle) -->
                <div class="flex md:hidden items-center space-x-2">
                    <a href="{{ route('shop.categories') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 flex items-center space-x-1">
                        <i class="fa-solid fa-layer-group text-amber-500"></i>
                        <span>Categories</span>
                    </a>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 hover:bg-slate-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none;"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Storefront Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 px-4 pt-3 pb-6 space-y-2 text-xs font-bold shadow-xl"
             style="display: none;">
            
            <a href="{{ route('shop.index') }}" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-house text-amber-500"></i>
                <span>Home / Stock Catalog</span>
            </a>

            <a href="{{ route('shop.categories') }}" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-layer-group text-amber-500"></i>
                <span>Product Categories</span>
            </a>

            <a href="{{ route('shop.my-orders') }}" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800">
                <i class="fa-solid fa-box-archive text-amber-500"></i>
                <span>Track My Orders</span>
            </a>

            <button @click="toggleTheme()" type="button" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-800 text-left">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-half-stroke text-amber-500"></i>
                    <span>Toggle Theme Mode</span>
                </div>
                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400" x-text="theme"></span>
            </button>

            @auth
                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-black">
                        <span>Admin Dashboard</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full py-2.5 text-center text-rose-500 font-bold hover:underline">
                            Logout of Account
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs py-12 mt-16 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="font-display font-black text-lg text-slate-900 dark:text-white">ATLAS COLLECTION</span>
                </div>
                <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed">
                    Nigeria's premier luxury streetwear collection. Preview available stock and place instant orders directly with us via WhatsApp.
                </p>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold leading-normal">
                    <i class="fa-solid fa-location-dot text-amber-500 mr-1"></i> {{ config('services.store.location') }}
                </p>
            </div>

            <!-- Social Media & Quick Links -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Connect & Channels</h4>
                <div class="flex flex-col space-y-2">
                    <a href="{{ config('services.social.instagram') }}" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center space-x-2 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span>Instagram: @atlasunisex</span>
                    </a>

                    <a href="{{ config('services.social.facebook') }}" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center space-x-2 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Facebook: Atlas Collection</span>
                    </a>

                    <a href="{{ config('services.social.tiktok') }}" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center space-x-2 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.82.57-1.33 1.51-1.42 2.51-.12 1.18.34 2.38 1.22 3.16.89.78 2.14 1.06 3.29.74 1.12-.3 2.05-1.18 2.38-2.28.18-.58.23-1.2.22-1.81V.02z"/>
                        </svg>
                        <span>TikTok: @atlasunisex</span>
                    </a>
                </div>
            </div>

            <!-- Direct Contact Info -->
            <div class="space-y-2">
                <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Direct Contact & Sales</h4>
                <p><i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i> WhatsApp & Call: <a href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">{{ config('services.store.phone') }}</a></p>
                <p><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> Email: <a href="mailto:{{ config('services.store.email') }}" class="text-slate-800 dark:text-slate-200 font-medium hover:underline">{{ config('services.store.email') }}</a></p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-center text-[11px] text-slate-500">
            &copy; {{ date('Y') }} Atlas Collection Bauchi, Nigeria. | Powered by <a href="https://harkone.com.ng" target="_blank" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">Harkone Designs</a>
        </div>
    </footer>

</body>
</html>
