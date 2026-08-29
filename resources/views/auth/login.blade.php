<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      class="h-full" 
      x-data="{ 
          theme: localStorage.getItem('theme') || 'light' 
      }" 
      x-init="document.documentElement.classList.toggle('dark', theme === 'dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Login | Atlas Collection</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

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
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-4 transition-colors">

    <div class="w-full max-w-md space-y-6">

        <!-- Logo & Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex p-2 bg-white rounded-3xl shadow-xl shadow-amber-500/10 mb-1 border border-amber-400/50">
                <img src="{{ asset('logo.png') }}" alt="Atlas Collection Logo" class="h-20 sm:h-24 w-auto object-contain">
            </div>
            <div>
                <h1 class="text-2xl font-black font-display tracking-wider text-slate-900 dark:text-white">ATLAS</h1>
                <p class="text-xs text-amber-600 dark:text-amber-400 font-extrabold uppercase tracking-widest -mt-0.5">COLLECTION</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 italic font-serif mt-1">...your style, our identity</p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 transition-colors">
            
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 text-center">
                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">
                    Staff & Admin Portal
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Sign In to Dashboard</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Enter your registered staff phone number and password</p>
            </div>

            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-3 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 rounded-xl text-rose-700 dark:text-rose-400 text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 rounded-xl text-rose-700 dark:text-rose-400 text-xs font-medium space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Phone + Password Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="login-form">
                @csrf

                <!-- Phone Number -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="phone" id="phone-input" value="{{ old('phone') }}" required 
                               placeholder="e.g. 08012345678"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-amber-500 transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Password <span class="text-rose-500">*</span></label>
                        <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 hover:underline transition-colors">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password-input" required 
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-amber-500 transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 dark:text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-100 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-amber-500 focus:ring-amber-500">
                        <span class="ml-2">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-md transition-all transform active:scale-95">
                    Sign In to Staff Module
                </button>
            </form>

        </div>

        <div class="text-center">
            <a href="{{ route('shop.index') }}" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-amber-500 transition-colors">
                &larr; Back to Public Stock Catalog
            </a>
        </div>

    </div>

</body>
</html>
