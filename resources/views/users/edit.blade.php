@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Edit Data User</h1>
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
            <h2 class="text-lg font-bold text-slate-900">Formulir Edit User</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi detail untuk user <strong>{{ $user->name }}</strong>.</p>
        </div>
        <div class="p-6">
            <form action="/users/{{ $user->id }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ $user->name }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ $user->email }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password Baru <span class="text-xs text-slate-400 font-normal ml-1">(Kosongkan jika tidak ingin diubah)</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="password" class="w-full px-4 py-2.5 pr-12 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password', 'eye-password')" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none" tabindex="-1">
                            <i id="eye-password" class="fa-regular fa-eye text-base"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Role Akses</label>
                    <select name="role" id="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User Biasa</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <a href="/users" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection