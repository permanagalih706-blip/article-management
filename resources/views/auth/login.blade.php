<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Article App</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-indigo-500 selection:text-white flex min-h-screen">

    <!-- Left Side: Image/Branding -->
    <div class="hidden lg:flex w-1/2 bg-[#1e293b] flex-col justify-center items-center p-12 relative overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full text-white fill-current">
                <polygon points="0,100 100,0 100,100"/>
            </svg>
        </div>
        
        <div class="relative z-10 text-center text-white max-w-lg">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-500 mb-8 shadow-lg shadow-indigo-500/30">
                <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C8 4.5 5 7.5 5 11c0 2 1.5 3.5 3.5 3.5 1.5 0 2.5-1 3.5-2.5 1-1.5 2-2.5 3.5-2.5 2 0 3.5 1.5 3.5 3.5 0 3.5-3 6.5-7 6.5-4 0-7-3-7-6.5h2c0 2.5 2.5 4.5 5 4.5 3 0 5-2 5-4.5 0-1-.5-1.5-1.5-1.5-1.5 0-2.5 1-3.5 2.5-1 1.5-2 2.5-3.5 2.5C6 16.5 3 14 3 11c0-4.5 4-8.5 9-8.5 4.5 0 8.5 4 8.5 8.5h-2c0-3.5-2.5-6.5-6.5-6.5z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-4 tracking-tight">Article App</h1>
            <p class="text-slate-300 text-lg leading-relaxed">Platform terbaik untuk menulis, mengelola, dan membagikan artikel-artikel berkualitas kepada jutaan pembaca.</p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-white">
        <div class="w-full max-w-md">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center gap-3 mb-10 text-slate-900">
                <svg class="w-8 h-8 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C8 4.5 5 7.5 5 11c0 2 1.5 3.5 3.5 3.5 1.5 0 2.5-1 3.5-2.5 1-1.5 2-2.5 3.5-2.5 2 0 3.5 1.5 3.5 3.5 0 3.5-3 6.5-7 6.5-4 0-7-3-7-6.5h2c0 2.5 2.5 4.5 5 4.5 3 0 5-2 5-4.5 0-1-.5-1.5-1.5-1.5-1.5 0-2.5 1-3.5 2.5-1 1.5-2 2.5-3.5 2.5C6 16.5 3 14 3 11c0-4.5 4-8.5 9-8.5 4.5 0 8.5 4 8.5 8.5h-2c0-3.5-2.5-6.5-6.5-6.5z"/>
                </svg>
                <span class="text-2xl font-bold">Article App</span>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">Selamat Datang!</h2>
                <p class="text-slate-500">Silakan login untuk mengakses akun Anda.</p>
            </div>
            <div class="mb-6">
                <a href="/"
                class="inline-flex items-center text-sm text-slate-600 hover:text-indigo-600">
                    ← Kembali ke Beranda
                </a>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 bg-slate-50 focus:bg-white" placeholder="nama@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Lupa password?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 bg-slate-50 focus:bg-white" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password', 'eye-login')" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none" tabindex="-1">
                            <i id="eye-login" class="fa-regular fa-eye text-base"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="remember_me" class="ml-2 block text-sm text-slate-600 cursor-pointer">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Log in
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-slate-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Daftar sekarang</a>
            </div>
        </div>
    </div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>
