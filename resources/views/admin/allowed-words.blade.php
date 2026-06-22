@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-black text-slate-900 leading-tight">Kelola Daftar Blokir Kata</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar kata yang tidak diperbolehkan dalam komentar artikel.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Form Tambah Kata -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tambah Kata Blokir
                </h3>
                
                <form action="{{ route('admin.allowed-words.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="word" class="block text-sm font-semibold text-slate-700 mb-1">Kata / Frasa</label>
                            <input type="text" name="word" id="word" value="{{ old('word') }}" required 
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400" 
                                placeholder="Contoh: spam, kasar">
                            @error('word')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                            Tambah Kata
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Kata -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Daftar Kata Blokir
                    </h3>
                    <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-full">{{ $words->total() }} total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs uppercase tracking-wider font-bold">
                                <th class="px-6 py-4">Kata / Frasa</th>
                                <th class="px-6 py-4">Ditambahkan Oleh</th>
                                <th class="px-6 py-4">Tanggal Ditambahkan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @forelse($words as $word)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $word->word }}</td>
                                    <td class="px-6 py-4">{{ $word->creator->name ?? 'System' }}</td>
                                    <td class="px-6 py-4">{{ $word->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.allowed-words.destroy', $word->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kata ini dari daftar blokir?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all" title="Hapus Kata">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                        <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        Belum ada kata terblokir yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($words->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $words->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
