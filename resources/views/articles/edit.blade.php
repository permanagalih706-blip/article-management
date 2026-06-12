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
            <form action="/articles/{{ $article->id }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

                {{-- Cover Image Management --}}
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                    <h3 class="text-sm font-bold text-slate-900">Gambar Sampul (Cover Image)</h3>
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        @if($article->cover_image)
                            <div class="relative shrink-0 group">
                                <img src="{{ asset('storage/' . $article->cover_image) }}" alt="Current Cover" class="w-40 h-28 object-cover rounded-lg border border-slate-200 shadow-sm">
                                <div class="mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="clear_cover" value="1" class="rounded text-red-600 focus:ring-red-500 border-slate-300">
                                        <span class="text-xs font-medium text-red-600 hover:text-red-700">Hapus Sampul Saat Ini</span>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="w-40 h-28 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400 border border-slate-300 shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        <div class="flex-1 w-full">
                            <label for="cover_image" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Ganti / Unggah Gambar Sampul</label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 file:cursor-pointer hover:file:bg-indigo-100">
                            <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG, WEBP, GIF. Maksimal 5MB.</p>
                            @error('cover_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Add More Media Gallery --}}
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                    <h3 class="text-sm font-bold text-slate-900">Tambah Media Baru ke Galeri</h3>
                    <input type="file" name="media[]" id="media" multiple accept="image/*,video/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 file:cursor-pointer hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-400 mt-2">Dapat memilih beberapa file gambar atau video sekaligus. Maksimal 20MB per file.</p>
                    @error('media')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Penulis (Author)</label>
                        <input type="text" disabled value="{{ $article->user->name ?? 'Unknown' }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-100 text-slate-500 outline-none cursor-not-allowed">
                        <p class="text-xs text-slate-400 mt-1">Penulis artikel tidak dapat diubah.</p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status Publikasi</label>
                        <select name="status" id="status" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white" onchange="togglePublishDate()">
                            <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft (Simpan sebagai konsep)</option>
                            <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published (Publikasikan)</option>
                        </select>
                    </div>
                </div>

                <div id="publish_date_wrapper" class="hidden">
                    <label for="published_at" class="block text-sm font-medium text-slate-700 mb-2">Jadwal Publikasi</label>
                    <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white text-slate-700">
                    <p class="text-xs text-slate-400 mt-1">Biarkan kosong untuk langsung mempublikasikan saat ini juga (atau tetap menggunakan waktu yang lalu).</p>
                </div>

                {{-- Existing Media Gallery with Reordering & Caption editor --}}
                @if($article->media->count() > 0)
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-base font-bold text-slate-900 mb-4">Galeri Media Terlampir</h3>
                        <p class="text-xs text-slate-400 mb-4">Gunakan tombol <i class="fa-solid fa-arrow-up"></i> dan <i class="fa-solid fa-arrow-down"></i> untuk mengatur urutan tampilan. Anda juga dapat memperbarui teks keterangan (caption) atau menyetel gambar sebagai sampul utama.</p>
                        
                        <div id="media-list" class="space-y-4">
                            @foreach($article->media as $item)
                                <div class="media-item flex flex-col sm:flex-row gap-4 items-stretch sm:items-center bg-white p-4 border border-slate-200 rounded-xl hover:border-slate-300 transition-colors" data-id="{{ $item->id }}">
                                    <!-- Order input (hidden, managed by JS) -->
                                    <input type="hidden" name="media_orders[{{ $item->id }}]" class="media-order-input" value="{{ $item->order }}">
                                    
                                    <!-- Sorting controls -->
                                    <div class="flex sm:flex-col gap-2 justify-center items-center px-2 shrink-0 border-r border-slate-100">
                                        <button type="button" onclick="moveUp(this)" class="p-1 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded transition-colors" title="Move Up">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <span class="text-xs font-bold text-slate-400 w-5 text-center my-0.5 order-label"></span>
                                        <button type="button" onclick="moveDown(this)" class="p-1 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded transition-colors" title="Move Down">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </div>

                                    <!-- Media Preview Thumbnail -->
                                    <div class="w-24 h-16 shrink-0 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center relative shadow-sm">
                                        @if($item->type === 'image')
                                            <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->caption }}" class="w-full h-full object-cover">
                                            @if($article->cover_image === $item->file_path)
                                                <span class="absolute top-1 left-1 bg-indigo-600 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow uppercase tracking-wide">Cover</span>
                                            @endif
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/10">
                                                <svg class="w-6 h-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 2.236A1 1 0 0014 9v2a1 1 0 00.553.894l2 1A1 1 0 0018 12V8a1 1 0 00-1.447-.894l-2 1z"/></svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Caption Editor -->
                                    <div class="flex-1 min-w-0">
                                        <input type="text" name="media_captions[{{ $item->id }}]" value="{{ old('media_captions.' . $item->id, $item->caption) }}" placeholder="Tambahkan keterangan media..." class="w-full px-3 py-1.5 text-sm rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400">
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($item->type === 'image' && $article->cover_image !== $item->file_path)
                                            <button type="button" onclick="setAsCover('{{ route('articles.media.set-cover', [$article->id, $item->id]) }}')" class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-600 hover:text-white transition-all text-xs font-semibold shadow-sm" title="Jadikan Sampul">
                                                Set Sampul
                                            </button>
                                        @endif
                                        <button type="button" onclick="deleteMedia('{{ route('media.destroy', $item->id) }}')" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 rounded-lg transition-all" title="Hapus Media">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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

{{-- Helper forms for out-of-form actions --}}
<form id="set-cover-form" method="POST" class="hidden">
    @csrf
</form>

<form id="delete-media-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    function togglePublishDate() {
        const status = document.getElementById('status').value;
        const wrapper = document.getElementById('publish_date_wrapper');
        if (status === 'published') {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }

    function setAsCover(url) {
        if (confirm('Jadikan gambar ini sebagai sampul artikel?')) {
            const form = document.getElementById('set-cover-form');
            form.action = url;
            form.submit();
        }
    }

    function deleteMedia(url) {
        if (confirm('Apakah Anda yakin ingin menghapus media ini secara permanen dari server?')) {
            const form = document.getElementById('delete-media-form');
            form.action = url;
            form.submit();
        }
    }

    // Media item reordering script
    function updateMediaIndices() {
        const list = document.getElementById('media-list');
        if (!list) return;
        const items = list.getElementsByClassName('media-item');
        for (let i = 0; i < items.length; i++) {
            // Update the display label
            const label = items[i].getElementsByClassName('order-label')[0];
            if (label) label.textContent = i + 1;

            // Update the hidden input value
            const input = items[i].getElementsByClassName('media-order-input')[0];
            if (input) input.value = i;
        }
    }

    function moveUp(button) {
        const item = button.closest('.media-item');
        const prev = item.previousElementSibling;
        if (prev && prev.classList.contains('media-item')) {
            item.parentNode.insertBefore(item, prev);
            updateMediaIndices();
        }
    }

    function moveDown(button) {
        const item = button.closest('.media-item');
        const next = item.nextElementSibling;
        if (next && next.classList.contains('media-item')) {
            item.parentNode.insertBefore(next, item);
            updateMediaIndices();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        togglePublishDate();
        updateMediaIndices();
    });
</script>
@endpush