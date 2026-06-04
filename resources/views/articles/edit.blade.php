@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Edit Artikel</h1>
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
            <h2 class="text-lg font-bold text-slate-900">Formulir Edit Artikel</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui konten dan informasi artikel ini.</p>
        </div>
        <div class="p-6">
            <form action="/articles/{{ $article->id }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

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
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required class="w-full px-4 py-2.5 rounded-lg border {{ $errors->has('title') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400">
                    @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-slate-700 mb-2">Penulis (Author)</label>
                        <select name="user_id" id="user_id" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $article->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status Publikasi</label>
                        @if($article->status === 'published')
                            <input type="hidden" name="status" value="published">
                            <select name="status" id="status" disabled class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-100 text-slate-500 outline-none appearance-none">
                                <option value="published" selected>Published (Locked)</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Artikel yang sudah diterbitkan tidak dapat diubah kembali menjadi draft.</p>
                        @else
                            <select name="status" id="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                                <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft (Simpan sebagai konsep)</option>
                                <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published (Publikasikan sekarang)</option>
                            </select>
                        @endif
                    </div>

                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-slate-700 mb-2">Isi Artikel</label>
                    <textarea name="content" id="content" rows="12" required class="w-full px-4 py-3 rounded-lg border {{ $errors->has('content') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 resize-y">{{ old('content', $article->content) }}</textarea>
                    @error('content')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <a href="/dashboard" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Update Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection