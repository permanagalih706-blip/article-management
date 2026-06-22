@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ isset($isDraftsPage) ? 'Draft Artikel Saya' : 'Blog' }}</h1>
    <a href="/articles/create" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create Article
    </a>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center text-emerald-700 gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Admin Moderation Panel --}}
    @if(auth()->check() && auth()->user()->role === 'superadmin')
        @php
            $pendingReports = \App\Models\CommentReport::with(['comment.user', 'comment.article', 'reporter'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        @endphp

        @if($pendingReports->count() > 0)
            <div class="mb-10 bg-rose-50/50 border border-rose-200/80 rounded-2xl p-6 shadow-sm animate-fade-in">
                <div class="flex items-center justify-between mb-4 border-b border-rose-200/60 pb-3">
                    <h3 class="text-lg font-black text-rose-950 flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                        Laporan Komentar Baru (Pending)
                    </h3>
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-rose-700 hover:text-rose-900 transition-colors uppercase tracking-wider">
                        Lihat Semua Laporan &rarr;
                    </a>
                </div>

                <div class="divide-y divide-rose-200/40">
                    @foreach($pendingReports as $report)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="text-xs font-bold bg-rose-100 text-rose-800 px-2.5 py-0.5 rounded-lg">
                                        Alasan: {{ $report->reason }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        Dilaporkan oleh <strong>{{ $report->reporter->name ?? 'Unknown' }}</strong>
                                    </span>
                                    <span class="text-xs text-slate-400 font-medium">
                                        {{ $report->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if($report->comment)
                                    <p class="text-sm font-semibold text-slate-900 bg-white border border-rose-100 rounded-xl p-3 shadow-xs truncate max-w-2xl">
                                        "{{ $report->comment->body }}"
                                    </p>
                                    <span class="block text-xs text-slate-500 mt-1">
                                        Oleh: {{ $report->comment->user->name ?? 'Unknown' }} | Artikel: <a href="{{ route('articles.show', $report->comment->article->slug ?? '') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline font-semibold">{{ $report->comment->article->title ?? '' }}</a>
                                    </span>
                                @else
                                    <p class="text-sm text-slate-400 italic bg-white border border-rose-100 rounded-xl p-3 shadow-xs">
                                        [Komentar telah dihapus]
                                    </p>
                                @endif
                                @if($report->description)
                                    <p class="text-xs text-rose-700/80 mt-1.5 italic font-medium">
                                        Catatan tambahan pelapor: "{{ $report->description }}"
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="delete_comment">
                                    <button type="submit" class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                                        Hapus Komentar
                                    </button>
                                </form>
                                <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="inline m-0">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="dismiss">
                                    <button type="submit" class="px-3.5 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-colors shadow-xs">
                                        Abaikan
                                    </button>
                                </form>
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="px-3.5 py-2 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition-colors shadow-xs">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- Search Bar -->
    <div class="flex justify-center mb-12">
        <form action="{{ request()->url() }}" method="GET" class="w-full max-w-4xl flex flex-col sm:flex-row items-stretch sm:items-center shadow-sm rounded-xl overflow-hidden border border-slate-200 bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-400 transition-all">
            
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-12 pr-4 py-3.5 text-base text-slate-700 bg-transparent border-none focus:ring-0 placeholder:text-slate-400" placeholder="Search for article...">
            </div>

            @if(!isset($isDraftsPage))
                <div class="h-px sm:h-8 w-full sm:w-px bg-slate-200"></div>

                <select name="author" class="px-4 py-3.5 text-slate-600 bg-transparent border-none focus:ring-0 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[position:right_0.5rem_center] pr-10 cursor-pointer outline-none">
                    <option value="">All Author</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('author') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <div class="h-px sm:h-8 w-full sm:w-px bg-slate-200"></div>

            <select name="sort" class="px-4 py-3.5 text-slate-600 bg-transparent border-none focus:ring-0 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[position:right_0.5rem_center] pr-10 cursor-pointer outline-none">
                <option value="latest_created" {{ request('sort') == 'latest_created' ? 'selected' : '' }}>Terbaru</option>
                <option value="latest_updated" {{ request('sort') == 'latest_updated' ? 'selected' : '' }}>Update Terbaru</option>
                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul A-Z</option>
                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul Z-A</option>
            </select>

            <button type="submit" class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors sm:rounded-r-xl sm:rounded-l-none">
                Search
            </button>
        </form>
    </div>

    <!-- Pagination Info Top -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <div class="text-slate-500 mb-4 sm:mb-0">
            @if($articles->total() > 0)
                Showing <strong>{{ $articles->firstItem() }}</strong> to <strong>{{ $articles->lastItem() }}</strong> of <strong>{{ $articles->total() }}</strong> results
            @else
                Showing 0 results
            @endif
        </div>
        
        <div>
            {{ $articles->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($articles as $article)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-shadow duration-300 flex flex-col h-full relative group overflow-hidden">
                
                <!-- Cover Image / Gradient Placeholder -->
                @if($article->cover_image)
                    <div class="w-full h-48 overflow-hidden shrink-0">
                        <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-slate-700 to-indigo-955 flex items-center justify-center relative overflow-hidden shrink-0">
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                        <span class="text-white/20 text-6xl font-black uppercase tracking-wider select-none">{{ substr($article->title, 0, 2) }}</span>
                    </div>
                @endif

                <!-- Admin Actions (Edit/Delete) - visible on hover for CRUD functionality -->
                @if(auth()->check() && (auth()->user()->role === 'superadmin' || auth()->id() === $article->user_id))
                <div class="absolute top-4 right-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    <a href="/articles/{{ $article->id }}/edit" class="p-1.5 bg-yellow-50/90 text-yellow-600 rounded-md hover:bg-yellow-500 hover:text-white transition-colors shadow-sm" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                    <form action="/articles/{{ $article->id }}" method="POST" onsubmit="return confirm('Delete this article?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 bg-red-50/90 text-red-600 rounded-md hover:bg-red-500 hover:text-white transition-colors shadow-sm" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                @endif

                <div class="p-6 flex flex-col flex-grow">
                    <!-- Category & Time -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex gap-2">
                            @if($article->status === 'draft')
                                <span class="bg-amber-50 text-amber-600 text-xs font-semibold px-3 py-1 rounded-full border border-amber-200">
                                    Draft
                                </span>
                            @elseif($article->published_at && $article->published_at->isFuture())
                                <span class="bg-indigo-50 text-indigo-600 text-xs font-semibold px-3 py-1 rounded-full border border-indigo-200" title="Terjadwal: {{ $article->published_at->format('d M Y H:i') }}">
                                    Scheduled
                                </span>
                            @else
                                <span class="bg-emerald-50 text-emerald-600 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-200">
                                    Published
                                </span>
                            @endif
                        </div>
                        <span class="text-sm text-slate-500 font-medium">
                            @if($article->published_at && $article->published_at->isFuture())
                                Terbit: {{ $article->published_at->diffForHumans() }}
                            @else
                                {{ $article->updated_at ? $article->updated_at->diffForHumans() : 'Just now' }}
                            @endif
                        </span>
                    </div>

                    <!-- Title -->
                    <h2 class="text-xl font-bold text-slate-900 mb-3 line-clamp-2 leading-snug hover:text-blue-600 transition-colors">
                        <a href="/articles/{{ $article->slug ?: $article->id }}">{{ $article->title }}</a>
                    </h2>

                    <!-- Excerpt -->
                    <p class="text-slate-500 mb-6 line-clamp-3 text-sm leading-relaxed flex-1">
                        {{ Str::limit(strip_tags($article->content), 120) }}
                    </p>

                    <!-- Footer: Author & Read More -->
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100 gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            @if(isset($article->user) && $article->user->profile_photo)
                                <img src="{{ asset('storage/' . $article->user->profile_photo) }}" alt="{{ $article->user->name }}" class="w-8 h-8 rounded-full object-cover shadow-sm shrink-0">
                            @else
                                @php
                                    $colors = ['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444'];
                                    $color = $colors[($article->user->id ?? 0) % 5];
                                    $initials = strtoupper(substr($article->user->name ?? '?', 0, 1));
                                @endphp
                                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold text-white shadow-sm shrink-0" style="background: {{ $color }};">
                                    {{ $initials }}
                                </div>
                            @endif
                            <span class="text-sm font-medium text-slate-700 truncate">{{ $article->user->name ?? 'Unknown' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-3 shrink-0">
                            @if($article->status === 'draft' && auth()->id() === $article->user_id)
                                <form action="{{ route('articles.publish', $article->id) }}" method="POST" class="inline m-0">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                        Publish
                                    </button>
                                </form>
                            @endif
                            <a href="/articles/{{ $article->slug ?: $article->id }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 group/link">
                                Read
                                <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    @if(request('search') || request('author') || request('sort'))
                        <p class="text-slate-500">No articles found matching your filters.</p>
                        <a href="{{ request()->url() }}" class="mt-4 px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors text-sm font-medium">Clear Filters</a>
                    @else
                        <p class="text-slate-500 mb-4">No articles found.</p>
                        <a href="/articles/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">Create first article</a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Info Bottom -->
    @if($articles->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $articles->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection