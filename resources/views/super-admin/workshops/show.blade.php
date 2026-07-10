<x-app-layout>
    @slot('pageTitle', 'Detail Bengkel - ' . $workshop->name)
    @slot('breadcrumb', 'Admin / Workshops / Detail')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Back Button & Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Bengkel
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Detail Bengkel</h1>
                    <p class="text-sm text-zinc-500 mt-0.5">Informasi profil mitra, data legalitas, status operasional, dan daftar staff bengkel.</p>
                </div>
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

        @if($errors->any())
            <div style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px;">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Workshop Identity & Status Change Form --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Profile Info --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <div class="text-center mb-6">
                        @if($workshop->logo_url)
                            <img src="{{ asset('storage/' . $workshop->logo_url) }}" alt="{{ $workshop->name }}" class="w-16 h-16 rounded-2xl object-cover mx-auto mb-3 border border-[#2E3030]">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-red-950/40 border border-red-900/40 text-red-400 font-bold text-2xl flex items-center justify-center mx-auto mb-3">
                                {{ strtoupper(substr($workshop->name, 0, 1)) }}
                            </div>
                        @endif
                        <h2 class="text-lg font-bold text-zinc-100">{{ $workshop->name }}</h2>
                        <p class="text-xs text-zinc-500 mt-1">Daftar sejak: {{ $workshop->created_at->translatedFormat('d M Y') }}</p>
                    </div>

                    <div class="space-y-4 border-t border-[#2E3030] pt-4 text-sm text-zinc-300">
                        <div>
                            <span class="text-xs text-zinc-500 block">Rating Rata-Rata</span>
                            <span class="font-semibold text-zinc-200">⭐ {{ number_format($workshop->rating_average, 1) }} / 5.0</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Jam Operasional</span>
                            <span class="font-semibold text-zinc-200">{{ $workshop->operational_hours ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Email Bengkel</span>
                            <span class="font-semibold text-zinc-200">{{ $workshop->email ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Nomor Telepon</span>
                            <span class="font-semibold text-zinc-200">{{ $workshop->phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Alamat Bengkel</span>
                            <span class="font-semibold text-zinc-200">{{ $workshop->full_address }}</span>
                        </div>
                    </div>
                </div>

                {{-- Owner & Legal Doc Card --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-zinc-200 mb-3 border-b border-[#2E3030] pb-2">Owner & Dokumen Legalitas</h3>
                    <div class="space-y-4 text-sm text-zinc-300">
                        <div>
                            <span class="text-xs text-zinc-500 block">Nama Pemilik</span>
                            <span class="font-semibold text-zinc-200">{{ $workshop->owner_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Nomor KTP Pemilik</span>
                            <span class="font-semibold text-zinc-200 font-mono">{{ $workshop->owner_ktp_number ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Dokumen Legalitas</span>
                            @if($workshop->legal_document_url)
                                <a href="{{ asset('storage/' . $workshop->legal_document_url) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-400 hover:text-blue-300 mt-1">
                                    Buka Dokumen Legalitas (PDF/Gambar)
                                </a>
                            @else
                                <span class="text-zinc-600 text-xs italic block mt-1">Tidak ada dokumen legalitas terupload</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Change Form --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg space-y-4">
                    <h3 class="text-sm font-bold text-zinc-200 border-b border-[#2E3030] pb-2">Kontrol Status Bengkel</h3>
                    
                    <form action="{{ route('admin.workshops.update', $workshop->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Status Selection --}}
                        <div>
                            <label class="text-xs text-zinc-500 block mb-1">Status Verifikasi</label>
                            <select name="status" id="workshop-status-select"
                                    class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                                <option value="pending" {{ $workshop->status === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="approved" {{ $workshop->status === 'approved' ? 'selected' : '' }}>Terverifikasi (Approved)</option>
                                <option value="rejected" {{ $workshop->status === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                                <option value="revision_needed" {{ $workshop->status === 'revision_needed' ? 'selected' : '' }}>Revisi Diperlukan</option>
                            </select>
                        </div>

                        {{-- Active Switch --}}
                        <div class="flex items-center justify-between py-2 border-t border-b border-[#2E3030]/50 my-2">
                            <span class="text-xs text-zinc-400 font-semibold">Status Operasional (Aktif)</span>
                            <div class="flex items-center">
                                <select name="is_active" class="bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-1.5 text-xs">
                                    <option value="1" {{ $workshop->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$workshop->is_active ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        {{-- Rejection / Revision Reason Area --}}
                        <div id="rejection-reason-block" class="{{ in_array($workshop->status, ['rejected', 'revision_needed']) ? '' : 'hidden' }}">
                            <label class="text-xs text-zinc-500 block mb-1">Alasan Penolakan / Kebutuhan Revisi</label>
                            <textarea name="rejection_reason" rows="3"
                                      placeholder="Masukkan alasan penolakan atau revisi dokumen legalitas..."
                                      class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:border-red-500 transition-colors">{{ $workshop->rejection_reason }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-red-655 hover:bg-red-555 border border-red-900/40 text-white text-sm font-bold rounded-xl transition-all shadow-md mt-2">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Column: Performance Stats & Staff List --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Stats Summary --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-2">Performa & Transaksi Servis</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-center">
                        <div class="p-4 bg-zinc-900/50 rounded-xl border border-[#2E3030]/50">
                            <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-wider">Total Servis Diselesaikan</span>
                            <span class="text-lg font-bold text-zinc-100 mt-1 block">{{ $totalServices }}</span>
                        </div>
                        <div class="p-4 bg-zinc-900/50 rounded-xl border border-[#2E3030]/50">
                            <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-wider">Total Pendapatan Bengkel</span>
                            <span class="text-lg font-bold text-zinc-100 mt-1 block">Rp {{ number_format($totalEarnings) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Staff List Card --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-base font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-3">Daftar Staf Bengkel ({{ $workshop->staff->count() }})</h3>
                    
                    @if($workshop->staff->isEmpty())
                        <div class="text-center py-12 text-zinc-650 bg-zinc-950/20 border border-dashed border-zinc-800 rounded-xl">
                            <svg class="w-12 h-12 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-xs">Belum ada staff terdaftar di bengkel ini.</span>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-[#2E3030] text-xs font-semibold uppercase tracking-wider text-zinc-400">
                                        <th class="py-3 pr-4">Nama Staf</th>
                                        <th class="py-3 px-4">Kontak</th>
                                        <th class="py-3 px-4">Posisi</th>
                                        <th class="py-3 px-4">Tanggal Bergabung</th>
                                        <th class="py-3 pl-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-[#2E3030]/50">
                                    @foreach($workshop->staff as $member)
                                        <tr class="align-middle">
                                            <td class="py-4 pr-4 font-bold text-zinc-250">
                                                {{ $member->user->name ?? '-' }}
                                            </td>
                                            <td class="py-4 px-4 text-zinc-350">
                                                <div>{{ $member->user->email ?? '-' }}</div>
                                                <div class="text-xs text-zinc-550 mt-0.5">{{ $member->user->phone_number ?? '-' }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                @if($member->position === 'admin')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-900 border border-zinc-700 text-zinc-300">
                                                        Admin Bengkel
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-900 border border-zinc-700 text-zinc-300">
                                                        Mekanik
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 text-zinc-400">
                                                {{ $member->joined_at ? $member->joined_at->translatedFormat('d M Y') : ($member->created_at ? $member->created_at->translatedFormat('d M Y') : '-') }}
                                            </td>
                                            <td class="py-4 pl-4 text-right">
                                                @if($member->is_active)
                                                    <span class="text-xs text-emerald-450 font-semibold">Aktif</span>
                                                @else
                                                    <span class="text-xs text-zinc-550 font-semibold">Nonaktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Toggle Script for Status select --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectEl = document.getElementById('workshop-status-select');
            const reasonBlock = document.getElementById('rejection-reason-block');

            if (selectEl && reasonBlock) {
                selectEl.addEventListener('change', function () {
                    const status = this.value;
                    if (status === 'rejected' || status === 'revision_needed') {
                        reasonBlock.classList.remove('hidden');
                    } else {
                        reasonBlock.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
