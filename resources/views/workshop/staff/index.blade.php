<x-app-layout>
    @slot('pageTitle', 'Kelola Staf')
    @slot('breadcrumb', 'Workshop / Staf')

    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- ── Alert Success/Error ── --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-950/30 border border-emerald-900/40 text-emerald-400 rounded-xl text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ── Header & Action ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Kelola Staf</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Kelola daftar pegawai dan mekanik di bengkel Anda</p>
            </div>
            <a href="{{ route('workshop.staff.create') }}"
               class="flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-red-900/30 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tambah Staf
            </a>
        </div>

        {{-- ── Search & Filter ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('workshop.staff.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari staf berdasarkan nama, email, atau nomor telepon..."
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <button type="submit"
                        class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('workshop.staff.index') }}"
                       class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- ── Staff Table ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Nama Staf</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Kontak</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Posisi</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Tanggal Bergabung</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($staffMembers as $member)
                            <tr class="hover:bg-zinc-900/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-200">{{ $member->user->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-300">{{ $member->user->email }}</div>
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ $member->user->phone_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($member->position === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-950/30 border border-blue-900/40 text-blue-400">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-950/30 border border-amber-900/40 text-amber-400">
                                            Mekanik
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($member->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/30 border border-emerald-900/40 text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-300">
                                    {{ $member->joined_at ? $member->joined_at->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('workshop.staff.edit', $member) }}"
                                           class="w-8 h-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 flex items-center justify-center text-zinc-400 hover:text-zinc-100 transition-colors"
                                           title="Ubah">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form method="POST" action="{{ route('workshop.staff.destroy', $member) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus staf ini? Semua data histori terkait tidak akan hilang, namun akun login staf akan dihapus secara permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-lg bg-red-950/40 hover:bg-red-900/50 border border-red-900/30 hover:border-red-650 flex items-center justify-center text-red-400 hover:text-red-300 transition-colors"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Tidak Ada Staf</p>
                                    <p class="text-zinc-650 text-xs mt-1">Belum ada staf yang ditambahkan atau tidak sesuai pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ── --}}
            @if($staffMembers->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $staffMembers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
