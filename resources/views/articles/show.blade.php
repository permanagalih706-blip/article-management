@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-2 text-slate-500 text-sm">
        <a href="/dashboard" class="hover:text-slate-900 transition-colors">Blog</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-slate-900 font-medium truncate max-w-[200px] sm:max-w-md">{{ $article->title }}</span>
    </div>
    <a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors text-sm border border-slate-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Blog
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <!-- Article Container -->
    <article class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Header Banner Image Placeholder/Gradient -->
        <div class="h-48 sm:h-64 bg-gradient-to-r from-slate-800 to-indigo-900 flex items-center justify-center p-8 relative">
            <div class="absolute inset-0 bg-slate-950/20"></div>
            <div class="relative text-center">
                @if($article->status === 'published')
                    <span class="bg-emerald-500/20 text-emerald-300 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-500/30 uppercase tracking-wider">
                        Published
                    </span>
                @else
                    <span class="bg-amber-500/20 text-amber-300 text-xs font-semibold px-3 py-1 rounded-full border border-amber-500/30 uppercase tracking-wider">
                        Draft
                    </span>
                @endif
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-4 tracking-tight leading-tight max-w-2xl mx-auto drop-shadow-sm">
                    {{ $article->title }}
                </h1>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            
            <!-- Author Meta Row -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-8">
                <div class="flex items-center gap-3">
                    @if($article->user && $article->user->profile_photo)
                        <img src="{{ asset('storage/' . $article->user->profile_photo) }}" alt="{{ $article->user->name }}" class="w-11 h-11 rounded-full object-cover ring-2 ring-slate-100 shadow-sm shrink-0">
                    @else
                        @php
                            $colors = ['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444'];
                            $color = $colors[($article->user->id ?? 0) % 5];
                            $initials = strtoupper(substr($article->user->name ?? '?', 0, 1));
                        @endphp
                        <div class="flex items-center justify-center w-11 h-11 rounded-full text-sm font-bold text-white shadow-sm shrink-0" style="background: {{ $color }};">
                            {{ $initials }}
                        </div>
                    @endif
                    <div>
                        <span class="block text-sm font-semibold text-slate-900 leading-none">{{ $article->user->name ?? 'Unknown' }}</span>
                        <span class="text-xs text-slate-500 mt-1 block">Penulis Artikel</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="block text-sm text-slate-600 font-medium">{{ $article->updated_at ? $article->updated_at->format('d M Y') : 'Just now' }}</span>
                    <span class="text-xs text-slate-400 block mt-1">Terakhir Diupdate: {{ $article->updated_at ? $article->updated_at->diffForHumans() : 'Just now' }}</span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="prose max-w-none text-slate-700 leading-relaxed text-base sm:text-lg whitespace-pre-line space-y-4">
                {{ $article->content }}
            </div>

            <!-- Actions Footer -->
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->id() === $article->user_id))
            <div class="mt-12 pt-6 border-t border-slate-100 flex justify-end gap-3">
                @if($article->status === 'draft' && auth()->id() === $article->user_id)
                <form action="{{ route('articles.publish', $article->id) }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Terbitkan Artikel
                    </button>
                </form>
                @endif

                <a href="/articles/{{ $article->id }}/edit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-semibold rounded-lg transition-colors border border-yellow-200 text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Artikel
                </a>

                <form action="/articles/{{ $article->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-lg transition-colors border border-red-200 text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Artikel
                    </button>
                </form>
            </div>
            @endif

        </div>
    </article>
</div>
@endsection