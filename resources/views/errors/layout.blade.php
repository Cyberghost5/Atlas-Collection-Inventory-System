<!DOCTYPE html>
<html lang="en" x-data="{ 
    theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', this.theme);
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error - Atlas Collection Bauchi')</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900|cinzel:700,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Cinzel', 'serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-amber-500 selection:text-slate-950 transition-colors duration-300">
    
    <!-- Top Header Bar -->
    <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('shop.index') }}" class="flex items-center space-x-3 group">
                <div class="p-1.5 bg-white rounded-2xl shadow-sm border border-amber-400/50 group-hover:scale-105 transition-transform">
                    <img src="{{ asset('logo.png') }}" alt="Atlas Collection Logo" class="h-9 sm:h-10 w-auto object-contain">
                </div>
                <div>
                    <span class="font-display font-black text-lg sm:text-xl tracking-wider text-slate-900 dark:text-white group-hover:text-amber-500 transition-colors">ATLAS</span>
                    <span class="block text-[10px] uppercase font-extrabold text-amber-600 dark:text-amber-400 tracking-widest -mt-1">COLLECTION</span>
                </div>
            </a>

            <div class="flex items-center space-x-3">
                <button @click="toggleTheme()" 
                        type="button" 
                        title="Toggle Light / Dark Mode" 
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-amber-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors text-xs font-bold flex items-center space-x-1.5">
                    <template x-if="theme === 'dark'">
                        <span class="flex items-center space-x-1">
                            <i class="fa-solid fa-sun text-amber-400"></i>
                            <span class="hidden sm:inline">Light Mode</span>
                        </span>
                    </template>
                    <template x-if="theme === 'light'">
                        <span class="flex items-center space-x-1">
                            <i class="fa-solid fa-moon text-slate-700"></i>
                            <span class="hidden sm:inline">Dark Mode</span>
                        </span>
                    </template>
                </button>

                <a href="{{ route('shop.index') }}" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow transition-all flex items-center space-x-1">
                    <i class="fa-solid fa-store"></i>
                    <span>Storefront</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-1 flex items-center justify-center p-6 sm:p-12">
        <div class="max-w-xl w-full text-center space-y-6">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 text-xs text-slate-500 dark:text-slate-400 transition-colors">
        <div class="max-w-7xl mx-auto px-4 text-center space-y-2">
            <p class="font-semibold">
                <i class="fa-solid fa-location-dot text-amber-500 mr-1"></i> Wunti market, Bababa plaza, shop E7 Block E (Beside New Flyover), Bauchi, Nigeria
            </p>
            <p>
                <i class="fa-brands fa-whatsapp text-emerald-500 mr-1"></i> Phone / WhatsApp: <a href="https://wa.me/2348103996947" target="_blank" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">0810 399 6947</a> | Email: <a href="mailto:atlascollection6@gmail.com" class="hover:underline">atlascollection6@gmail.com</a>
            </p>
            <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                &copy; {{ date('Y') }} Atlas Collection Bauchi. All rights reserved. | Powered by <a href="https://harkone.com.ng" target="_blank" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">Harkone Designs</a>
            </p>
        </div>
    </footer>

</body>
</html>
