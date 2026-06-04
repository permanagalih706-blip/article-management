@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Tambah User Baru</h1>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <div class="mb-6">
        <a href="/users" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar User
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">Formulir Tambah User</h2>
            <p class="text-sm text-slate-500 mt-1">Masukkan informasi detail untuk user baru.</p>
        </div>
        <div class="p-6">
            <form action="/users" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="Contoh: Budi Santoso">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" id="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="budi@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 pr-12 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePassword('password', 'eye-password')" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none" tabindex="-1">
                            <i id="eye-password" class="fa-regular fa-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Role Akses</label>
                    <select name="role" id="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                        <option value="user">User Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <a href="/users" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection