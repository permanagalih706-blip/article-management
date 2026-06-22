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

    <!-- ==================== RATING SECTION ==================== -->
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Rating Artikel
                </h3>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-3xl font-black text-slate-900" id="average-rating-display">{{ $averageRating }}</span>
                    <div class="flex flex-col">
                        <!-- Average Stars Display -->
                        <div class="flex items-center gap-0.5" id="average-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @elseif($i - $averageRating < 1 && $i - $averageRating > 0)
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <defs><linearGradient id="half-star"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#e2e8f0"/></linearGradient></defs>
                                        <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-slate-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-slate-500 mt-0.5"><span id="rating-count-display">{{ $ratingCount }}</span> penilaian</span>
                    </div>
                </div>
            </div>

            {{-- Interactive Star Rating --}}
            @auth
            <div class="flex flex-col items-center sm:items-end gap-2">
                <span class="text-xs text-slate-500 font-medium">Rating Anda:</span>
                <div class="flex items-center gap-1" id="star-rating-input">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="submitRating({{ $i }})" 
                            class="star-btn p-0.5 transition-all duration-200 hover:scale-125 focus:outline-none focus:scale-125"
                            data-value="{{ $i }}"
                            title="{{ $i }} bintang">
                            <svg class="w-8 h-8 {{ $userRating && $userRating->value >= $i ? 'text-amber-400' : 'text-slate-300 hover:text-amber-300' }} transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    @endfor
                </div>
                <span id="rating-feedback" class="text-xs text-emerald-600 font-medium hidden"></span>
            </div>
            @else
            <div class="text-sm text-slate-500">
                <a href="/login" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">Login</a> untuk memberikan rating
            </div>
            @endauth
        </div>
    </div>

    <!-- ==================== COMMENTS SECTION ==================== -->
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-6">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Komentar
            <span class="text-sm font-medium text-slate-400">({{ $comments->count() }})</span>
        </h3>

        {{-- Comment Form --}}
        @auth
        <div class="mb-8">
            <form action="{{ route('comments.store', $article->id) }}" method="POST">
                @csrf
                <div class="flex items-start gap-3">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100 shadow-sm shrink-0 mt-1">
                    @else
                        @php
                            $colors = ['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444'];
                            $avatarColor = $colors[(auth()->id() ?? 0) % 5];
                            $avatarInitials = strtoupper(substr(auth()->user()->name ?? '?', 0, 1));
                        @endphp
                        <div class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold text-white shadow-sm shrink-0 mt-1" style="background: {{ $avatarColor }};">
                            {{ $avatarInitials }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <textarea name="body" rows="3" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all placeholder:text-slate-400" placeholder="Tulis komentar Anda..." required></textarea>
                        @error('body')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Kirim Komentar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @else
        <div class="mb-8 bg-slate-50 border border-slate-200 rounded-xl p-5 text-center">
            <p class="text-sm text-slate-600">
                <a href="/login" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">Login</a> untuk menulis komentar
            </p>
        </div>
        @endauth

        {{-- Comments List --}}
        <div class="space-y-4" id="comments-list">
            @forelse($comments as $comment)
                @include('components.comment-item', ['comment' => $comment, 'article' => $article, 'depth' => 0])
            @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <p class="text-slate-400 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    // ==================== Gallery Slider ====================
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

    // ==================== Rating ====================
    function submitRating(value) {
        const articleId = {{ $article->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || '{{ csrf_token() }}';

        fetch(`/articles/${articleId}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ value: value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update average display
                document.getElementById('average-rating-display').textContent = data.average;
                document.getElementById('rating-count-display').textContent = data.count;

                // Update interactive star colors
                document.querySelectorAll('.star-btn').forEach(btn => {
                    const btnValue = parseInt(btn.getAttribute('data-value'));
                    const svg = btn.querySelector('svg');
                    if (btnValue <= data.userRating) {
                        svg.classList.remove('text-slate-300', 'hover:text-amber-300');
                        svg.classList.add('text-amber-400');
                    } else {
                        svg.classList.remove('text-amber-400');
                        svg.classList.add('text-slate-300', 'hover:text-amber-300');
                    }
                });

                // Show feedback
                const feedback = document.getElementById('rating-feedback');
                feedback.textContent = data.message;
                feedback.classList.remove('hidden');
                setTimeout(() => feedback.classList.add('hidden'), 3000);
            }
        })
        .catch(error => console.error('Rating error:', error));
    }

    // ==================== Scroll to Parent Comment and Highlight ====================
    function scrollToComment(commentId) {
        const target = document.getElementById('comment-' + commentId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            const card = target.querySelector('.comment-card');
            if (card) {
                card.classList.add('ring-4', 'ring-blue-500/20', 'bg-blue-50/50');
                setTimeout(() => {
                    card.classList.remove('ring-4', 'ring-blue-500/20', 'bg-blue-50/50');
                }, 2000);
            }
        }
    }

    // ==================== Comment Forms Toggle ====================
    function toggleReplyForm(commentId) {
        const form = document.getElementById('reply-form-' + commentId);
        if (form) {
            form.classList.toggle('hidden');
        }
    }

    function toggleEditForm(commentId) {
        const form = document.getElementById('edit-form-' + commentId);
        const body = document.getElementById('comment-body-' + commentId);
        if (form) {
            form.classList.toggle('hidden');
            if (body) body.classList.toggle('hidden');
        }
    }

    function toggleReportForm(commentId) {
        const form = document.getElementById('report-form-' + commentId);
        if (form) {
            form.classList.toggle('hidden');
        }
    }

    // ==================== Star Hover Effect ====================
    const starButtons = document.querySelectorAll('.star-btn');
    starButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            const hoverValue = parseInt(this.getAttribute('data-value'));
            starButtons.forEach(b => {
                const bVal = parseInt(b.getAttribute('data-value'));
                const svg = b.querySelector('svg');
                if (bVal <= hoverValue) {
                    svg.classList.add('text-amber-400');
                    svg.classList.remove('text-slate-300');
                }
            });
        });
    });

    const starContainer = document.getElementById('star-rating-input');
    if (starContainer) {
        starContainer.addEventListener('mouseleave', function() {
            // Reset to current user rating
            const userRating = {{ $userRating ? $userRating->value : 0 }};
            starButtons.forEach(btn => {
                const btnValue = parseInt(btn.getAttribute('data-value'));
                const svg = btn.querySelector('svg');
                if (btnValue <= userRating) {
                    svg.classList.add('text-amber-400');
                    svg.classList.remove('text-slate-300', 'hover:text-amber-300');
                } else {
                    svg.classList.remove('text-amber-400');
                    svg.classList.add('text-slate-300', 'hover:text-amber-300');
                }
            });
        });
    }
</script>
@endpush
@endsection