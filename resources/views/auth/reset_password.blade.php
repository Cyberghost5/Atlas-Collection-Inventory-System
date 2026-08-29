<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password | Atlas Collection</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
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
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950 flex items-center justify-center p-4">

    <div class="w-full max-w-md space-y-6">

        <div class="text-center space-y-2">
            <h1 class="text-2xl font-black font-display tracking-wider text-indigo-400">SET NEW PASSWORD</h1>
            <p class="text-xs text-slate-400 font-medium uppercase tracking-widest">Phone Verification Code</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            @if(session('success'))
                <div class="p-3 bg-emerald-950/60 border border-emerald-800/80 rounded-xl text-emerald-400 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-rose-950/60 border border-rose-800/80 rounded-xl text-rose-400 text-xs font-medium space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $phone) }}" readonly required 
                           class="w-full px-4 py-2.5 bg-slate-950/50 border border-slate-800 rounded-xl text-xs text-slate-400 font-mono">
                </div>

                <!-- 6-digit Code -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">6-Digit Verification Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. 849201"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">New Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required placeholder="Min 6 characters"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Confirm New Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat new password"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-xs rounded-xl shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                    Reset Password
                </button>
            </form>

        </div>

    </div>

</body>
</html>
