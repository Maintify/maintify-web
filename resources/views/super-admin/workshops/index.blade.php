<x-app-layout>
    @slot('pageTitle', 'Workshop Management')
    @slot('breadcrumb', 'Admin / Workshops')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Workshop Management</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Kelola data seluruh bengkel mitra Maintify, status verifikasi, dan performa pemeliharaannya.</p>
            </div>
            <div>
                <a href="{{ route('admin.workshops.pending') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-xl transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Antrean Verifikasi
                </a>
            </div>
        </div>

        {{-- Success / Error Flash Messages --}}
        @if(session('success'))
            <div style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Search & Filters --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('admin.workshops.index') }}" class="flex flex-col md:flex-row gap-3">
                {{-- Search Box --}}
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama bengkel, email, kota, pemilik..."
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="w-full md:w-56">
                    <select name="status"
                            class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="revision_needed" {{ $status === 'revision_needed' ? 'selected' : '' }}>Revisi Diperlukan</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all flex-1 md:flex-initial">
                        Cari
                    </button>
                    @if($search || $status)
                        <a href="{{ route('admin.workshops.index') }}"
                           class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Workshops Table --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Bengkel</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Pemilik</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Lokasi</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Status Aktif</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($workshops as $ws)
                            <tr class="hover:bg-zinc-900/30 transition-colors align-middle">
                                {{-- Workshop name --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-200">
                                        <a href="{{ route('admin.workshops.show', $ws->id) }}" class="hover:text-red-400 transition-colors">
                                            {{ $ws->name }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-zinc-500 mt-0.5">{{ $ws->email ?? '-' }}</div>
                                </td>
                                {{-- Owner --}}
                                <td class="px-6 py-4 text-sm font-semibold text-zinc-300">
                                    {{ $ws->owner_name ?? ($ws->user->name ?? '-') }}
                                </td>
                                {{-- Location --}}
                                <td class="px-6 py-4 text-sm text-zinc-350">
                                    {{ $ws->city ?? '-' }}, {{ $ws->province ?? '-' }}
                                </td>
                                {{-- Status badge --}}
                                <td class="px-6 py-4">
                                    @if($ws->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400">
                                            Terverifikasi
                                        </span>
                                    @elseif($ws->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950/40 border border-red-900/50 text-red-400">
                                            Ditolak
                                        </span>
                                    @elseif($ws->status === 'revision_needed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-950/40 border border-amber-900/50 text-amber-400">
                                            Revisi Diperlukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-950/40 border border-zinc-900/50 text-zinc-400">
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </td>
                                {{-- Active/Inactive status --}}
                                <td class="px-6 py-4">
                                    @if($ws->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a href="{{ route('admin.workshops.show', $ws->id) }}" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-300 text-xs font-semibold rounded-lg transition-all">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Tidak Ada Bengkel Mitra</p>
                                    <p class="text-zinc-650 text-xs mt-1">Tidak ada data bengkel yang terdaftar atau cocok dengan pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($workshops->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $workshops->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
