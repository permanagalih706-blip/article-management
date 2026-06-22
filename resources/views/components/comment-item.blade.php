@props(['comment', 'article', 'depth' => 0])

<div class="comment-item {{ $depth > 0 ? 'ml-6 sm:ml-10 border-l-2 border-indigo-100 pl-4 sm:pl-6' : '' }}" id="comment-{{ $comment->id }}">
    <div class="comment-card bg-white rounded-xl p-4 sm:p-5 {{ $depth === 0 ? 'border border-slate-200 shadow-sm' : 'border border-slate-100' }} transition-all hover:border-slate-300">
        
        {{-- Comment Header --}}
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                @if($comment->user && $comment->user->profile_photo)
                    <img src="{{ asset('storage/' . $comment->user->profile_photo) }}" alt="{{ $comment->user->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-100 shadow-sm shrink-0">
                @else
                    @php
                        $colors = ['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444'];
                        $color = $colors[($comment->user->id ?? 0) % 5];
                        $initials = strtoupper(substr($comment->user->name ?? '?', 0, 1));
                    @endphp
                    <div class="flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold text-white shadow-sm shrink-0" style="background: {{ $color }};">
                        {{ $initials }}
                    </div>
                @endif
                <div>
                    <span class="font-semibold text-sm text-slate-900">{{ $comment->user->name ?? 'Unknown' }}</span>
                    @if($comment->user && $comment->user->role === 'superadmin')
                        <span class="ml-1 text-[10px] font-bold bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded-full uppercase tracking-wide">Admin</span>
                    @endif
                    
                    {{-- Reply target badge --}}
                    @if($comment->parent_id && $comment->parent)
                        <button type="button" onclick="scrollToComment({{ $comment->parent_id }})" class="ml-1 inline-flex items-center gap-0.5 text-[10px] font-black text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-1.5 py-0.5 rounded transition-all uppercase tracking-wider shadow-2xs border border-blue-200" title="Klik untuk melihat komentar asli">
                            <i class="fa-solid fa-reply text-[8px] transform -scale-x-100"></i>
                            {{ $comment->parent->user->name ?? 'User' }}
                        </button>
                    @endif

                    <span class="block text-xs text-slate-400 mt-0.5">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Actions Dropdown --}}
            @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="4" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="10" cy="16" r="1.5"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50">
                    {{-- Reply --}}
                    <button onclick="toggleReplyForm({{ $comment->id }})" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Balas
                    </button>

                    {{-- Edit (owner only) --}}
                    @if(auth()->id() === $comment->user_id)
                    <button onclick="toggleEditForm({{ $comment->id }})" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                    @endif

                    {{-- Delete (owner or admin) --}}
                    @if(auth()->id() === $comment->user_id || auth()->user()->role === 'superadmin')
                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                    @endif

                    {{-- Report (not own comment) --}}
                    @if(auth()->id() !== $comment->user_id)
                    <button onclick="toggleReportForm({{ $comment->id }})" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors border-t border-slate-100 mt-1 pt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        Laporkan
                    </button>
                    @endif
                </div>
            </div>
            @endauth
        </div>

        {{-- Comment Body --}}
        @if($comment->trashed())
            <p class="text-sm text-slate-400 italic bg-slate-50 px-3 py-2 rounded-lg">[Komentar telah dihapus]</p>
        @else
            <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line" id="comment-body-{{ $comment->id }}">{{ $comment->body }}</div>
        @endif

        {{-- Edit Form (hidden by default) --}}
        @auth
        @if(auth()->id() === $comment->user_id && !$comment->trashed())
        <div id="edit-form-{{ $comment->id }}" class="hidden mt-3">
            <form action="{{ route('comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea name="body" rows="3" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all" placeholder="Edit komentar...">{{ $comment->body }}</textarea>
                <div class="flex items-center gap-2 mt-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="toggleEditForm({{ $comment->id }})" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
        @endif
        @endauth

        {{-- Reply Form (hidden by default) --}}
        @auth
        @if(!$comment->trashed())
        <div id="reply-form-{{ $comment->id }}" class="hidden mt-3">
            <form action="{{ route('comments.store', $article->id) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="body" rows="2" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all" placeholder="Tulis balasan..." required></textarea>
                <div class="flex items-center gap-2 mt-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        Balas
                    </button>
                    <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
        @endif
        @endauth

        {{-- Report Form (hidden by default) --}}
        @auth
        @if(auth()->id() !== $comment->user_id && !$comment->trashed())
        <div id="report-form-{{ $comment->id }}" class="hidden mt-3">
            <form action="{{ route('comments.report', $comment->id) }}" method="POST" class="bg-red-50 border border-red-200 rounded-xl p-4">
                @csrf
                <p class="text-sm font-semibold text-red-800 mb-3">Laporkan Komentar</p>
                <select name="reason" onchange="toggleCustomReason(this, {{ $comment->id }})" required class="w-full px-3 py-2 border border-red-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-2">
                    <option value="">-- Pilih Alasan --</option>
                    <option value="spam">Spam</option>
                    <option value="abusive">Konten Kasar / Abusive</option>
                    <option value="harassment">Pelecehan / Harassment</option>
                    <option value="other">Lainnya (Tulis alasan sendiri)</option>
                </select>
                <div id="custom-reason-container-{{ $comment->id }}" class="hidden mb-2">
                    <input type="text" name="custom_reason" id="custom-reason-input-{{ $comment->id }}" class="w-full px-3 py-2 border border-red-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Tuliskan alasan Anda...">
                </div>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-red-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none" placeholder="Deskripsi tambahan (opsional)..."></textarea>
                <div class="flex items-center gap-2 mt-2">
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        Kirim Laporan
                    </button>
                    <button type="button" onclick="toggleReportForm({{ $comment->id }})" class="px-4 py-2 bg-white hover:bg-red-100 text-red-700 text-sm font-semibold rounded-lg transition-colors border border-red-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
        @endif
        @endauth
        <script>
            if (typeof toggleCustomReason === 'undefined') {
                window.toggleCustomReason = function(select, commentId) {
                    const container = document.getElementById('custom-reason-container-' + commentId);
                    const input = document.getElementById('custom-reason-input-' + commentId);
                    if (select.value === 'other') {
                        container.classList.remove('hidden');
                        input.setAttribute('required', 'required');
                    } else {
                        container.classList.add('hidden');
                        input.removeAttribute('required');
                    }
                }
            }
        </script>
    </div>

    {{-- Parallel Replies (only for top-level comments) --}}
    @if($depth === 0)
        @php
            $allReplies = $comment->getAllReplies();
        @endphp
        @if($allReplies->count() > 0)
            <div class="mt-3 space-y-3">
                @foreach($allReplies as $reply)
                    @include('components.comment-item', ['comment' => $reply, 'article' => $article, 'depth' => 1])
                @endforeach
            </div>
        @endif
    @endif
</div>
