<x-app-layout>
    @slot('pageTitle', 'Tinjau Pendaftaran - ' . $workshop->name)
    @slot('breadcrumb', 'Admin / Verifikasi / Tinjau')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Back Button & Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.workshops.pending') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Antrean
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Tinjau Kelayakan Bengkel</h1>
                    <p class="text-sm text-zinc-500 mt-0.5">Silakan periksa kevalidan dokumen legalitas dan kesesuaian data bengkel.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-950/40 border border-amber-900/50 text-amber-400">
                    Menunggu Verifikasi
                </span>
            </div>
        </div>

        @if($errors->any())
            <div style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px;">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Split Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Left: Details --}}
            <div class="space-y-6">
                {{-- Workshop Info --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-base font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-3">Informasi Bengkel</h3>
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">Nama Bengkel</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $workshop->name }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-zinc-500 block mb-1">No. Telepon</span>
                                <span class="text-sm font-semibold text-zinc-200">{{ $workshop->phone }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block mb-1">Email Bengkel</span>
                                <span class="text-sm font-semibold text-zinc-200">{{ $workshop->email }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">Jam Operasional</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $workshop->operational_hours }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">Alamat Lengkap</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $workshop->address }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div>
                                <span class="text-xs text-zinc-500 block mb-1">Kota</span>
                                <span class="text-sm font-semibold text-zinc-200">{{ $workshop->city }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block mb-1">Provinsi</span>
                                <span class="text-sm font-semibold text-zinc-200">{{ $workshop->province }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block mb-1">Kode Pos</span>
                                <span class="text-sm font-semibold text-zinc-200">{{ $workshop->postal_code ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Owner Info --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-base font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-3">Informasi Pemilik</h3>
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">Nama Pemilik</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $workshop->owner_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">NIK KTP</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $workshop->owner_ktp_number ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Documents Review --}}
            <div>
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg h-full flex flex-col">
                    <div class="flex items-center justify-between mb-4 border-b border-[#2E3030] pb-3">
                        <h3 class="text-base font-bold text-zinc-200">Dokumen Legalitas</h3>
                        @if($workshop->legal_document_url)
                            <a href="{{ asset('storage/' . $workshop->legal_document_url) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-400 hover:text-blue-300">
                                <svg style="width:14px;height:14px; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Buka di Tab Baru
                            </a>
                        @endif
                    </div>

                    <div class="flex-1 bg-zinc-950 border border-zinc-800 rounded-xl overflow-hidden min-h-[300px] flex items-center justify-center relative">
                        @if($workshop->legal_document_url)
                            @php
                                $extension = pathinfo($workshop->legal_document_url, PATHINFO_EXTENSION);
                                $isPdf = strtolower($extension) === 'pdf';
                            @endphp

                            @if($isPdf)
                                <iframe src="{{ asset('storage/' . $workshop->legal_document_url) }}" class="w-full h-full border-none absolute inset-0"></iframe>
                            @else
                                <img src="{{ asset('storage/' . $workshop->legal_document_url) }}" alt="Legal Document" class="max-w-full max-h-full object-contain p-2">
                            @endif
                        @else
                            <div class="text-center p-6 text-zinc-600">
                                <svg class="w-12 h-12 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <span class="text-xs">Tidak ada file dokumen legalitas yang diunggah.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Verification Actions Control Panel --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
            <h3 class="text-base font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-3">Keputusan Verifikasi</h3>
            
            <div id="action-buttons-group" class="flex flex-wrap gap-4">
                {{-- Approve Action --}}
                <form action="{{ route('admin.workshops.approve', $workshop->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI pendaftaran bengkel ini?')">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl transition-all shadow-md">
                        Setujui Pendaftaran
                    </button>
                </form>

                {{-- Trigger Reject Panel --}}
                <button type="button" onclick="showForm('reject-form-panel')" class="px-6 py-3 bg-red-650 hover:bg-red-550 text-white font-bold text-sm rounded-xl transition-all shadow-md">
                    Tolak Pendaftaran
                </button>

                {{-- Trigger Revision Panel --}}
                <button type="button" onclick="showForm('revision-form-panel')" class="px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold text-sm rounded-xl transition-all shadow-md">
                    Minta Revisi Data
                </button>
            </div>

            {{-- Rejection Form Panel --}}
            <div id="reject-form-panel" class="hidden mt-6 pt-6 border-t border-[#2E3030] animate-fadeIn">
                <form action="{{ route('admin.workshops.reject', $workshop->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-red-400 mb-2">Alasan Penolakan (Wajib diisi)</label>
                        <textarea name="rejection_reason" required placeholder="Jelaskan alasan detail pendaftaran ini ditolak..." rows="4" class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl p-3 text-sm focus:outline-none focus:border-red-500 transition-colors"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideAllForms()" class="px-4 py-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-red-650 hover:bg-red-550 text-white font-bold text-xs rounded-lg transition-all">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Revision Form Panel --}}
            <div id="revision-form-panel" class="hidden mt-6 pt-6 border-t border-[#2E3030] animate-fadeIn">
                <form action="{{ route('admin.workshops.revision', $workshop->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-amber-400 mb-2">Instruksi Revisi untuk Bengkel (Wajib diisi)</label>
                        <textarea name="rejection_reason" required placeholder="Sebutkan data atau dokumen apa saja yang perlu diperbaiki oleh calon mitra bengkel..." rows="4" class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl p-3 text-sm focus:outline-none focus:border-amber-500 transition-colors"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideAllForms()" class="px-4 py-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-lg transition-all">
                            Kirim Permintaan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showForm(panelId) {
            // Hide all panels first
            document.getElementById('reject-form-panel').classList.add('hidden');
            document.getElementById('revision-form-panel').classList.add('hidden');
            
            // Show the target panel
            document.getElementById(panelId).classList.remove('hidden');

            // Scroll down to the panel
            document.getElementById(panelId).scrollIntoView({ behavior: 'smooth' });
        }

        function hideAllForms() {
            document.getElementById('reject-form-panel').classList.add('hidden');
            document.getElementById('revision-form-panel').classList.add('hidden');
        }
    </script>
    @endpush

    <style>
        .animate-fadeIn {
            animation: fadeIn var(--transition-base);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>
