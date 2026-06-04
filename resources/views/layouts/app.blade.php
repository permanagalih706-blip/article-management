<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article App</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9; /* slate-100 */
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Topbar Navigation -->
    <nav class="bg-[#1e293b] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Menu -->
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="text-indigo-400">
                            <!-- Custom Wave Logo SVG -->
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C8 4.5 5 7.5 5 11c0 2 1.5 3.5 3.5 3.5 1.5 0 2.5-1 3.5-2.5 1-1.5 2-2.5 3.5-2.5 2 0 3.5 1.5 3.5 3.5 0 3.5-3 6.5-7 6.5-4 0-7-3-7-6.5h2c0 2.5 2.5 4.5 5 4.5 3 0 5-2 5-4.5 0-1-.5-1.5-1.5-1.5-1.5 0-2.5 1-3.5 2.5-1 1.5-2 2.5-3.5 2.5C6 16.5 3 14 3 11c0-4.5 4-8.5 9-8.5 4.5 0 8.5 4 8.5 8.5h-2c0-3.5-2.5-6.5-6.5-6.5z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:block">
                        <div class="flex items-baseline space-x-1">
                            <a href="/dashboard" class="px-4 py-2 rounded-md text-sm font-medium {{ request()->is('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }} transition-colors">Blog</a>
                            <a href="/drafts" class="px-4 py-2 rounded-md text-sm font-medium {{ request()->is('drafts') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }} transition-colors">Drafts</a>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <a href="/users" class="px-4 py-2 rounded-md text-sm font-medium {{ request()->is('users*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }} transition-colors">Users</a>
                            @endif
                            <a href="/about" class="px-4 py-2 rounded-md text-sm font-medium {{ request()->is('about') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }} transition-colors">About</a>
                            <a href="/contact" class="px-4 py-2 rounded-md text-sm font-medium {{ request()->is('contact') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }} transition-colors">Contact</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile & Logout -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <a href="/profile" class="text-sm text-slate-300 hover:text-white transition-colors">{{ auth()->user()->name }}</a>
                            @if(auth()->user()->profile_photo)
                                <img class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-700" src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="User Avatar">
                            @else
                                <img class="h-9 w-9 rounded-full ring-2 ring-slate-700" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff&size=36" alt="User Avatar">
                            @endif
                        </div>
                        <form action="/logout" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-red-500/20 hover:bg-red-500/40 rounded border border-red-500/30 transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="/login" class="px-4 py-2 text-sm font-medium text-white hover:text-slate-300 transition-colors">Login</a>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- Sub-header Banner (White) -->
    @hasSection('header')
        <div class="bg-white border-b border-slate-200 shadow-sm py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div id="flash-success" class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-500 hover:text-emerald-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div id="flash-error" class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 shadow-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
                <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
    @endif

    <!-- CONTENT -->
    <main class="flex-1 w-full flex flex-col">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-8 mt-12 bg-white border-t border-slate-200">
        <div class="px-6 mx-auto max-w-7xl">
            <p class="text-sm text-center text-slate-500">
                &copy; {{ date('Y') }} Article App. Built with Laravel & Tailwind CSS.
            </p>
        </div>
    </footer>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
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
@stack('scripts')
</body>
</html>