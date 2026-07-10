<x-app-layout>
    @slot('pageTitle', 'Antrean Verifikasi Bengkel')
    @slot('breadcrumb', 'Admin / Verifikasi Bengkel')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Antrean Verifikasi Bengkel</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Daftar calon bengkel mitra yang menunggu tinjauan kelayakan pendaftaran</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-950/40 border border-amber-900/50 text-amber-400">
                {{ $pendingWorkshops->total() }} Menunggu Tindakan
            </span>
        </div>

        @if(session('success'))
            <div style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Nama Bengkel</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Nama Pemilik</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Kontak & Lokasi</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Dokumen</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($pendingWorkshops as $workshop)
                            <tr class="hover:bg-zinc-900/30 transition-colors">
                                <td class="px-6 py-4 font-bold text-zinc-200">
                                    {{ $workshop->name }}
                                    <div class="text-[10px] font-normal text-zinc-500 mt-1">Daftar pada: {{ $workshop->created_at->translatedFormat('d M Y, H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-zinc-300">{{ $workshop->owner_name ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500 mt-0.5">NIK: {{ $workshop->owner_ktp_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-300">{{ $workshop->email }} &middot; {{ $workshop->phone }}</div>
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ $workshop->address }}, {{ $workshop->city }}, {{ $workshop->province }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($workshop->legal_document_url)
                                        <a href="{{ asset('storage/' . $workshop->legal_document_url) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-blue-400 hover:text-blue-300">
                                            <svg style="width:14px;height:14px; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Buka Dokumen
                                        </a>
                                    @else
                                        <span class="text-zinc-650 text-xs italic">Tidak ada dokumen</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.workshops.review', $workshop->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-950/20 hover:bg-red-950/40 border border-red-900/40 hover:border-red-900/70 text-red-400 hover:text-red-300 text-xs font-bold rounded-xl transition-all">
                                        Tinjau Kelayakan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-950/30 border border-emerald-900/30 text-emerald-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-zinc-300 text-sm font-semibold">Semua Bersih!</p>
                                    <p class="text-zinc-500 text-xs mt-1">Tidak ada calon bengkel mitra yang sedang menunggu verifikasi saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pendingWorkshops->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $pendingWorkshops->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
