@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Tulis Artikel Baru</h1>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <div class="mb-6">
        <a href="/dashboard" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-900">Formulir Artikel Baru</h2>
            <p class="text-sm text-slate-500 mt-1">Bagikan ide dan cerita Anda kepada dunia.</p>
        </div>
        <div class="p-6">
            <form action="/articles" method="POST" class="space-y-6">
                @csrf

                {{-- Validation Error Summary --}}
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-red-800 mb-1">Terdapat kesalahan pada form:</p>
                                <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Judul Artikel</label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}" class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400" placeholder="Masukkan judul yang menarik...">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status Publikasi</label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                            <option value="draft">Draft (Simpan sebagai konsep)</option>
                            <option value="published">Published (Publikasikan sekarang)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-slate-700 mb-2">Isi Artikel</label>
                    <textarea name="content" id="content" rows="10" required class="w-full px-4 py-3 rounded-lg border {{ $errors->has('content') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 resize-y" placeholder="Mulai menulis artikel Anda di sini...">{{ old('content') }}</textarea>
                    @error('content')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <a href="/dashboard" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Simpan Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection