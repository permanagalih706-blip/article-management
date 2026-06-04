@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Contact Us</h1>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Contact Form Column -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-bold text-slate-900">Kirim Pesan</h2>
                <p class="text-sm text-slate-500 mt-1">Kami sangat senang mendengar saran, masukan, atau pertanyaan dari Anda.</p>
            </div>
            
            <div class="p-6 sm:p-8">
                
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center text-green-700 gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="#" class="space-y-6" onsubmit="alert('Pesan berhasil terkirim! (Demo)'); return false;">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" id="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="Budi Santoso">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" id="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="budi@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-700 mb-2">Subjek Pesan</label>
                        <input type="text" name="subject" id="subject" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="Tuliskan subjek atau tujuan pesan...">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-slate-700 mb-2">Pesan Anda</label>
                        <textarea name="message" id="message" rows="6" required class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 resize-y" placeholder="Tuliskan pesan atau pertanyaan Anda secara detail di sini..."></textarea>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-3 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            Kirim Pesan Sekarang
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Contact Info Column -->
        <div class="space-y-6">
            
            <!-- Cards Panel -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-bold text-slate-900 mb-6">Informasi Kontak</h3>
                
                <div class="space-y-6">
                    
                    <!-- Item 1 -->
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Lokasi Kantor</span>
                            <span class="text-slate-700 text-sm mt-1 block font-medium leading-relaxed">
                                Jl. Example No. 123, Kota Example, Indonesia
                            </span>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Alamat Email</span>
                            <a href="mailto:example@gmail.com" class="text-blue-600 hover:underline text-sm mt-1 block font-medium">
                                example@gmail.com
                            </a>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nomor Telepon</span>
                            <span class="text-slate-700 text-sm mt-1 block font-medium">
                                +62 123 4567 890
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Social Media Panel -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-base font-bold text-slate-900 mb-4">Media Sosial</h3>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-sky-400 hover:text-white transition-all flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-pink-600 hover:text-white transition-all flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051c-.058 1.28-.072 1.688-.072 4.949 0 3.261.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.261 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.261-.014-3.669-.072-4.949-.2-4.358-2.617-6.78-6.979-6.98C15.668.014 15.261 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
