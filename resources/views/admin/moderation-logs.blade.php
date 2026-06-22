@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-black text-slate-900 leading-tight">Log Moderasi Otomatis</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar komentar yang ditolak secara otomatis oleh filter kata terlarang.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Audit Logs
            </h3>
            <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-full">{{ $logs->total() }} total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">Konten yang Diblokir</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Alasan / Detail</th>
                        <th class="px-6 py-4">Tanggal Kejadian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 max-w-xs md:max-w-md">
                                <p class="font-medium text-slate-900 bg-slate-50 rounded-lg p-2.5 border border-slate-100 text-xs whitespace-pre-wrap">{{ $log->blocked_content }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-700">{{ $log->user->name ?? 'Guest' }}</span>
                                <span class="block text-xs text-slate-400">{{ $log->user->email ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 px-2 py-1 rounded-lg">
                                    {{ $log->reason }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                                <span class="block text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Log moderasi kosong.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
