@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-black text-slate-900 leading-tight">Detail Laporan #{{ $report->id }}</h2>
        <p class="text-sm text-slate-500 mt-1">Review laporan komentar dan ambil tindakan moderasi.</p>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors text-sm border border-slate-200 shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Laporan
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Info Laporan -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Comment Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Konten Komentar yang Dilaporkan</h3>
                @if($report->comment)
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 mb-4">
                        <p class="text-slate-800 text-base leading-relaxed whitespace-pre-line">{{ $report->comment->body }}</p>
                        @if($report->comment->trashed())
                            <span class="inline-block mt-2 text-xs font-bold bg-red-100 text-red-800 px-2 py-0.5 rounded">Soft Deleted</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                        <span>Penulis: <strong class="text-slate-700">{{ $report->comment->user->name ?? 'Unknown' }}</strong></span>
                        <span>Artikel: <a href="{{ route('articles.show', $report->comment->article->slug ?? '') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold underline">{{ $report->comment->article->title ?? 'N/A' }}</a></span>
                    </div>
                @else
                    <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
                        <p class="text-red-700 italic text-sm">[Komentar telah dihapus secara permanen atau soft-delete]</p>
                    </div>
                @endif
            </div>

            <!-- Report Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Detail Laporan</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <span class="block text-xs text-slate-400 font-semibold uppercase">Pelapor</span>
                        <span class="text-slate-800 font-bold text-sm">{{ $report->reporter->name ?? 'Unknown' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400 font-semibold uppercase">Alasan</span>
                        <span class="capitalize text-slate-800 font-bold text-sm">{{ $report->reason }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400 font-semibold uppercase">Tanggal Laporan</span>
                        <span class="text-slate-800 font-semibold text-sm">{{ $report->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400 font-semibold uppercase">Status Saat Ini</span>
                        <span class="inline-block mt-0.5 text-xs font-bold px-2.5 py-1 rounded-lg
                            {{ $report->status === 'pending' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                            {{ $report->status === 'dismissed' ? 'bg-slate-100 text-slate-800' : '' }}
                        ">
                            {{ $report->status }}
                        </span>
                    </div>
                </div>

                @if($report->description)
                    <div class="border-t border-slate-100 pt-4">
                        <span class="block text-xs text-slate-400 font-semibold uppercase mb-1">Deskripsi Tambahan Pelapor</span>
                        <p class="text-slate-700 text-sm bg-slate-50 rounded-xl p-3 border border-slate-100 leading-relaxed">{{ $report->description }}</p>
                    </div>
                @endif
            </div>
            
        </div>

        <!-- Panel Tindakan / Resolusi -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm sticky top-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Tindakan Moderasi</h3>
                
                @if($report->status === 'pending')
                    <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="action" value="delete_comment" class="mt-1 text-indigo-600 focus:ring-indigo-500" checked>
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Hapus Komentar</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">Hapus komentar terlapor dan selesaikan laporan ini.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="action" value="dismiss" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Tolak Laporan</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">Abaikan laporan jika dirasa komentar tidak melanggar aturan.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="action" value="resolve" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="block text-sm font-bold text-slate-800">Selesaikan Tanpa Tindakan</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">Tandai laporan selesai tanpa melakukan apa-apa.</span>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                            Terapkan Keputusan
                        </button>
                    </form>
                @else
                    <div class="space-y-4">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-sm text-slate-600">
                            <p class="mb-2">Laporan ini telah diselesaikan:</p>
                            <div class="space-y-1.5 text-xs">
                                <p><strong>Oleh:</strong> {{ $report->resolver->name ?? 'Unknown' }}</p>
                                <p><strong>Tanggal:</strong> {{ $report->resolved_at ? $report->resolved_at->format('d M Y H:i') : '-' }}</p>
                                <p><strong>Tindakan:</strong> {{ $report->action_taken ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
