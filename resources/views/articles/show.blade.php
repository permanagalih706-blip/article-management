@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-2 text-slate-500 text-sm">
        <a href="{{ auth()->check() ? '/dashboard' : '/articles' }}" class="hover:text-slate-900 transition-colors">{{ auth()->check() ? 'Dashboard' : 'Blog' }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-slate-900 font-medium truncate max-w-[200px] sm:max-w-md">{{ $article->title }}</span>
    </div>
    <a href="{{ auth()->check() ? '/dashboard' : '/articles' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors text-sm border border-slate-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        {{ auth()->check() ? 'Kembali ke Dashboard' : 'Kembali ke Blog' }}
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <!-- Article Container -->
    <article class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="p-6 sm:p-10">
            
            <!-- Article Header Section -->
            <div class="mb-6">
                <!-- Status Badge with bold black borders and outline for maximum visibility -->
                <div class="mb-4">
                    @if($article->status === 'published')
                        @if($article->published_at && $article->published_at->isFuture())
                            <span class="bg-indigo-100 text-indigo-950 text-xs font-black px-3.5 py-1.5 rounded-lg border-2 border-slate-950 uppercase tracking-wider shadow-[2px_2px_0px_#000]">
                                Scheduled
                            </span>
                        @else
                            <span class="bg-emerald-100 text-emerald-950 text-xs font-black px-3.5 py-1.5 rounded-lg border-2 border-slate-950 uppercase tracking-wider shadow-[2px_2px_0px_#000]">
                                Published
                            </span>
                        @endif
                    @else
                        <span class="bg-amber-100 text-amber-950 text-xs font-black px-3.5 py-1.5 rounded-lg border-2 border-slate-950 uppercase tracking-wider shadow-[2px_2px_0px_#000]">
                            Draft
                        </span>
                    @endif
                </div>

                <!-- Title with solid black color and strong high contrast -->
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight leading-tight mt-2 mb-4">
                    {{ $article->title }}
                </h1>
            </div>

            <!-- Author Meta Row -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-200 mb-8">
                <div class="flex items-center gap-3">
                    @if($article->user && $article->user->profile_photo)
                        <img src="{{ asset('storage/' . $article->user->profile_photo) }}" alt="{{ $article->user->name }}" class="w-11 h-11 rounded-full object-cover ring-2 ring-slate-200 shadow-sm shrink-0">
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
                        <span class="block text-sm font-bold text-slate-950 leading-none">{{ $article->user->name ?? 'Unknown' }}</span>
                        <span class="text-xs text-slate-500 mt-1 block font-medium">Penulis Artikel</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="block text-sm text-slate-950 font-bold">
                        @if($article->status === 'published' && $article->published_at)
                            {{ $article->published_at->format('d M Y') }}
                        @else
                            {{ $article->updated_at ? $article->updated_at->format('d M Y') : 'Just now' }}
                        @endif
                    </span>
                    <span class="text-xs text-slate-400 block mt-1">Terakhir Diupdate: {{ $article->updated_at ? $article->updated_at->diffForHumans() : 'Just now' }}</span>
                </div>
            </div>

            <!-- Cover Image (displayed inline, no cropping, flows downward) -->
            @if($article->cover_image)
                <div class="mb-8 rounded-2xl overflow-hidden border-2 border-slate-950 shadow-[4px_4px_0px_#000] bg-slate-50">
                    <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-auto max-h-[600px] object-contain mx-auto">
                </div>
            @endif

            <!-- Gallery Slider -->
            @if($article->media->count() > 0)
                <div class="mb-10 bg-slate-50 border border-slate-200 rounded-2xl p-4 md:p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Galeri Media
                    </h3>
                    
                    <div class="relative overflow-hidden rounded-xl bg-slate-900 flex items-center justify-center min-h-[250px] md:min-h-[400px]" id="gallery-slider">
                        <!-- Slides -->
                        @foreach($article->media as $index => $item)
                            <div class="slide-item {{ $index === 0 ? 'block' : 'hidden' }} w-full h-[250px] md:h-[400px] flex flex-col justify-center relative" data-index="{{ $index }}">
                                @if($item->type === 'image')
                                    <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->caption }}" class="w-full h-full object-contain mx-auto">
                                @else
                                    <video controls class="w-full h-full object-contain bg-black mx-auto">
                                        <source src="{{ asset('storage/' . $item->file_path) }}">
                                        Browser Anda tidak mendukung tag video.
                                    </video>
                                @endif
                                
                                @if($item->caption)
                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/85 via-black/50 to-transparent p-4 text-white text-xs md:text-sm text-center">
                                        {{ $item->caption }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- Navigation Buttons (only if > 1 items) -->
                        @if($article->media->count() > 1)
                            <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-indigo-600 text-white p-2 rounded-full transition-colors z-10">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-indigo-600 text-white p-2 rounded-full transition-colors z-10">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        @endif
                    </div>

                    <!-- Thumbnails row -->
                    @if($article->media->count() > 1)
                        <div class="flex gap-2 overflow-x-auto mt-4 pb-2 scrollbar-thin">
                            @foreach($article->media as $index => $item)
                                <button onclick="goToSlide({{ $index }})" class="thumb-item w-16 h-12 rounded-md overflow-hidden border-2 {{ $index === 0 ? 'border-indigo-600 opacity-100' : 'border-transparent opacity-60 hover:opacity-90' }} transition-all shrink-0">
                                    @if($item->type === 'image')
                                        <img src="{{ asset('storage/' . $item->file_path) }}" alt="Thumb" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-slate-800 flex flex-col items-center justify-center text-slate-300 text-[10px] font-bold">
                                            <svg class="w-4 h-4 mb-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 2.236A1 1 0 0014 9v2a1 1 0 00.553.894l2 1A1 1 0 0018 12V8a1 1 0 00-1.447-.894l-2 1z"/></svg>
                                            VIDEO
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <!-- Content Area -->
            <div class="prose max-w-none text-slate-700 leading-relaxed text-base sm:text-lg whitespace-pre-line space-y-4">
                {{ $article->content }}
            </div>

            <!-- Actions Footer -->
            @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->id() === $article->user_id))
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

@push('scripts')
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide-item');
    const thumbs = document.querySelectorAll('.thumb-item');

    function showSlide(index) {
        if (slides.length === 0) return;
        
        // Pause any playing videos in the previous slide
        const prevVideo = slides[currentSlide].querySelector('video');
        if (prevVideo) {
            prevVideo.pause();
        }

        // Clamp index
        if (index >= slides.length) {
            currentSlide = 0;
        } else if (index < 0) {
            currentSlide = slides.length - 1;
        } else {
            currentSlide = index;
        }

        // Toggle visibility
        slides.forEach((slide, idx) => {
            if (idx === currentSlide) {
                slide.classList.remove('hidden');
                slide.classList.add('block');
            } else {
                slide.classList.remove('block');
                slide.classList.add('hidden');
            }
        });

        // Update highlight on thumbnails
        thumbs.forEach((thumb, idx) => {
            if (idx === currentSlide) {
                thumb.classList.remove('border-transparent', 'opacity-60');
                thumb.classList.add('border-indigo-600', 'opacity-100');
            } else {
                thumb.classList.remove('border-indigo-600', 'opacity-100');
                thumb.classList.add('border-transparent', 'opacity-60');
            }
        });
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    function goToSlide(index) {
        showSlide(index);
    }
</script>
@endpush
@endsection