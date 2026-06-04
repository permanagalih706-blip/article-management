<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Article App</title>
    
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
        <div class="absolute bottom-0 right-0 w-full h-full opacity-10">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full text-white fill-current">
                <polygon points="100,0 0,100 100,100"/>
            </svg>
        </div>
        
        <div class="relative z-10 text-center text-white max-w-lg">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-500 mb-8 shadow-lg shadow-indigo-500/30">
                <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C8 4.5 5 7.5 5 11c0 2 1.5 3.5 3.5 3.5 1.5 0 2.5-1 3.5-2.5 1-1.5 2-2.5 3.5-2.5 2 0 3.5 1.5 3.5 3.5 0 3.5-3 6.5-7 6.5-4 0-7-3-7-6.5h2c0 2.5 2.5 4.5 5 4.5 3 0 5-2 5-4.5 0-1-.5-1.5-1.5-1.5-1.5 0-2.5 1-3.5 2.5-1 1.5-2 2.5-3.5 2.5C6 16.5 3 14 3 11c0-4.5 4-8.5 9-8.5 4.5 0 8.5 4 8.5 8.5h-2c0-3.5-2.5-6.5-6.5-6.5z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-4 tracking-tight">Bergabunglah!</h1>
            <p class="text-slate-300 text-lg leading-relaxed">Buat akun Anda sekarang dan mulailah perjalanan Anda dalam menulis dan berbagi cerita kepada dunia.</p>
        </div>
    </div>

    <!-- Right Side: Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 bg-white">
        <div class="w-full max-w-md">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center gap-3 mb-8 text-slate-900">
                <svg class="w-8 h-8 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 4.5C8 4.5 5 7.5 5 11c0 2 1.5 3.5 3.5 3.5 1.5 0 2.5-1 3.5-2.5 1-1.5 2-2.5 3.5-2.5 2 0 3.5 1.5 3.5 3.5 0 3.5-3 6.5-7 6.5-4 0-7-3-7-6.5h2c0 2.5 2.5 4.5 5 4.5 3 0 5-2 5-4.5 0-1-.5-1.5-1.5-1.5-1.5 0-2.5 1-3.5 2.5-1 1.5-2 2.5-3.5 2.5C6 16.5 3 14 3 11c0-4.5 4-8.5 9-8.5 4.5 0 8.5 4 8.5 8.5h-2c0-3.5-2.5-6.5-6.5-6.5z"/>
                </svg>
                <span class="text-2xl font-bold">Article App</span>
            </div>

            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">Buat Akun Baru</h2>
                <p class="text-slate-500">Lengkapi data diri Anda di bawah ini.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 bg-slate-50 focus:bg-white" placeholder="Budi Santoso">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 bg-slate-50 focus:bg-white" placeholder="nama@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 bg-slate-50 focus:bg-white" placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('password', 'eye-password')" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none" tabindex="-1">
                            <i id="eye-password" class="fa-regular fa-eye text-base"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 bg-slate-50 focus:bg-white" placeholder="Ulangi password Anda">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none" tabindex="-1">
                            <i id="eye-confirm" class="fa-regular fa-eye text-base"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Daftar Akun
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">Masuk di sini</a>
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
