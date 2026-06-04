@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">About Us</h1>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
    
    <!-- Introduction Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-10">
        <div class="h-4 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        <div class="p-6 sm:p-10 text-center sm:text-left">
            <h2 class="text-2xl font-bold text-slate-900 mb-4">Misi Kami</h2>
            <p class="text-slate-600 text-lg leading-relaxed max-w-3xl">
                Kami membangun platform manajemen artikel ini untuk mempermudah berbagi ilmu, mengekspresikan gagasan, dan menghubungkan orang-orang melalui tulisan berkualitas. Desain minimalis, performa cepat, dan kemudahan kolaborasi adalah pilar utama kami.
            </p>
        </div>
    </div>

    <!-- Core Values Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        
        <!-- Value 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Performa Cepat</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                Dioptimalkan untuk waktu pemuatan yang sangat cepat, memberikan kenyamanan saat membaca dan menulis.
            </p>
        </div>

        <!-- Value 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Sistem Keamanan</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                Data dan informasi profil Anda terlindungi dengan enkripsi standar industri dan validasi ketat.
            </p>
        </div>

        <!-- Value 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Desain Responsif</h3>
            <p class="text-slate-500 text-sm leading-relaxed">
                Tampilan antarmuka yang sangat dinamis dan responsif pada perangkat mobile, tablet, maupun desktop.
            </p>
        </div>

    </div>

    <!-- Team Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-slate-900">Developer Team</h2>
            <p class="text-slate-500 text-sm mt-2">Orang-orang kreatif di balik layar yang membangun aplikasi ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            
            <!-- Member 1 -->
            <div class="flex flex-col items-center text-center">
                <img class="w-24 h-24 rounded-full object-cover shadow-md ring-4 ring-slate-100 mb-4 bg-slate-50" src="https://ui-avatars.com/api/?name=Example+Developer+1&background=3b82f6&color=fff&size=96" alt="Example Developer 1">
                <h3 class="text-base font-bold text-slate-900">Example Developer 1</h3>
                <p class="text-xs text-blue-600 font-semibold uppercase mt-1 tracking-wider">Full Stack Developer</p>
                <p class="text-slate-500 text-sm mt-3 leading-relaxed max-w-[240px]">
                    Bertanggung jawab atas arsitektur basis data, logika sistem backend, dan integrasi API utama.
                </p>
            </div>

            <!-- Member 2 -->
            <div class="flex flex-col items-center text-center">
                <img class="w-24 h-24 rounded-full object-cover shadow-md ring-4 ring-slate-100 mb-4 bg-slate-50" src="https://ui-avatars.com/api/?name=Example+Developer+2&background=6366f1&color=fff&size=96" alt="Example Developer 2">
                <h3 class="text-base font-bold text-slate-900">Example Developer 2</h3>
                <p class="text-xs text-indigo-600 font-semibold uppercase mt-1 tracking-wider">UI/UX Designer</p>
                <p class="text-slate-500 text-sm mt-3 leading-relaxed max-w-[240px]">
                    Merancang alur pengguna, tata letak antarmuka, dan estetika visual aplikasi agar terasa modern.
                </p>
            </div>

            <!-- Member 3 -->
            <div class="flex flex-col items-center text-center">
                <img class="w-24 h-24 rounded-full object-cover shadow-md ring-4 ring-slate-100 mb-4 bg-slate-50" src="https://ui-avatars.com/api/?name=Example+Developer+3&background=10b981&color=fff&size=96" alt="Example Developer 3">
                <h3 class="text-base font-bold text-slate-900">Example Developer 3</h3>
                <p class="text-xs text-emerald-600 font-semibold uppercase mt-1 tracking-wider">Frontend Engineer</p>
                <p class="text-slate-500 text-sm mt-3 leading-relaxed max-w-[240px]">
                    Membangun komponen web interaktif, animasi, dan memastikan responsivitas desain pada semua perangkat.
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
