@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-black text-slate-900 leading-tight">Laporan Komentar</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar laporan komentar dari pengguna untuk dimoderasi.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <!-- Filter Status -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" 
            class="px-4 py-2 text-sm font-semibold rounded-xl transition-all border {{ $status === 'pending' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Pending
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" 
            class="px-4 py-2 text-sm font-semibold rounded-xl transition-all border {{ $status === 'resolved' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Selesai (Resolved)
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'dismissed']) }}" 
            class="px-4 py-2 text-sm font-semibold rounded-xl transition-all border {{ $status === 'dismissed' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Ditolak (Dismissed)
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'all']) }}" 
            class="px-4 py-2 text-sm font-semibold rounded-xl transition-all border {{ $status === 'all' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Semua
        </a>
    </div>

    <!-- Tabel Daftar Laporan -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Daftar Laporan
            </h3>
            <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-full">{{ $reports->total() }} total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">Komentar Terlapor</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 max-w-xs md:max-w-md">
                                @if($report->comment)
                                    <p class="font-semibold text-slate-900 truncate">{{ $report->comment->body }}</p>
                                    <span class="text-xs text-slate-400">Oleh: {{ $report->comment->user->name ?? 'Unknown' }} | Artikel: {{ $report->comment->article->title ?? 'Deleted' }}</span>
                                @else
                                    <p class="text-slate-400 italic">[Komentar telah dihapus]</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $report->reporter->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                <span class="capitalize px-2 py-1 text-xs font-bold rounded-lg 
                                    {{ $report->reason === 'spam' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $report->reason === 'abusive' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $report->reason === 'harassment' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $report->reason === 'other' ? 'bg-slate-100 text-slate-800' : '' }}
                                ">
                                    {{ $report->reason }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold px-2 py-1 rounded-lg
                                    {{ $report->status === 'pending' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $report->status === 'dismissed' ? 'bg-slate-100 text-slate-800' : '' }}
                                ">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.reports.show', $report->id) }}" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 transition-colors shadow-xs">
                                    Detail / Tindakan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Tidak ada laporan komentar yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
