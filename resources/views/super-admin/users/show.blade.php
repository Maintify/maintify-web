<x-app-layout>
    @slot('pageTitle', 'Detail User - ' . $user->name)
    @slot('breadcrumb', 'Admin / Users / Detail')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Back Button & Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Pengguna
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Detail Pengguna</h1>
                    <p class="text-sm text-zinc-500 mt-0.5">Informasi lengkap, riwayat hubungan data, dan kontrol status aktif pengguna.</p>
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
            {{-- Left Panel: Profile Info & Status Controls --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Profile Info Card --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-red-950/40 border border-red-900/40 text-red-400 font-bold text-2xl flex items-center justify-center mx-auto mb-3">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h2 class="text-lg font-bold text-zinc-100">{{ $user->name }}</h2>
                        <p class="text-xs text-zinc-500 mt-1">Daftar sejak: {{ $user->created_at->translatedFormat('d M Y') }}</p>
                    </div>

                    <div class="space-y-4 border-t border-[#2E3030] pt-4">
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">Email</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $user->email }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">No. Telepon</span>
                            <span class="text-sm font-semibold text-zinc-200">{{ $user->phone_number ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block mb-1">Peran Akses</span>
                            @if($user->isSuperAdmin())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950/40 border border-red-900/50 text-red-400">
                                    Super Admin
                                </span>
                            @elseif($user->isWorkshop())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-950/40 border border-blue-900/50 text-blue-400">
                                    Bengkel Mitra
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400">
                                    Pemilik Kendaraan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Widget --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-2">Kontrol Status Akun</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-zinc-500">Status Saat Ini</span>
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-zinc-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                        @if($user->id === auth()->id())
                            <div class="text-xs text-zinc-650 italic bg-zinc-900/50 border border-zinc-800 p-3 rounded-xl mt-2">
                                Anda tidak dapat menonaktifkan akun sendiri untuk mencegah terkunci dari sistem.
                            </div>
                        @else
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif pengguna ini?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                @if($user->is_active)
                                    <button type="submit" class="w-full py-2.5 bg-red-650 hover:bg-red-550 border border-red-900/40 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                                        Nonaktifkan Akun
                                    </button>
                                    <p class="text-[11px] text-zinc-600 mt-2 text-center">Pengguna akan segera dikeluarkan dan tidak dapat masuk kembali.</p>
                                @else
                                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 border border-emerald-900/40 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                                        Aktifkan Akun
                                    </button>
                                    <p class="text-[11px] text-zinc-600 mt-2 text-center">Pengguna akan dapat masuk kembali ke sistem.</p>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Panel: Related Entities --}}
            <div class="lg:col-span-2 space-y-6">
                @if($user->isVehicleOwner())
                    {{-- Customer: Vehicles List --}}
                    <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                        <h3 class="text-base font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-3">Kendaraan Terdaftar ({{ $user->vehicles->count() }})</h3>
                        
                        @if($user->vehicles->isEmpty())
                            <div class="text-center py-12 text-zinc-600">
                                <svg class="w-12 h-12 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                                </svg>
                                <span class="text-xs">Pengguna ini belum mendaftarkan kendaraan apapun.</span>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-[#2E3030] text-xs font-semibold uppercase tracking-wider text-zinc-400">
                                            <th class="py-3 pr-4">Plat Nomor</th>
                                            <th class="py-3 px-4">Brand / Model</th>
                                            <th class="py-3 px-4">Tahun</th>
                                            <th class="py-3 px-4">Bahan Bakar</th>
                                            <th class="py-3 pl-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-[#2E3030]/50">
                                        @foreach($user->vehicles as $vehicle)
                                            <tr>
                                                <td class="py-4 pr-4">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-950/30 border border-red-900/40 text-red-400">
                                                        {{ $vehicle->plate_number }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4 font-semibold text-zinc-200">
                                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                                </td>
                                                <td class="py-4 px-4 text-zinc-350">{{ $vehicle->year }}</td>
                                                <td class="py-4 px-4 text-zinc-350">{{ $vehicle->fuel_type ?? '-' }}</td>
                                                <td class="py-4 pl-4 text-right">
                                                    @if($vehicle->is_active)
                                                        <span class="text-xs text-emerald-400 font-semibold">Aktif</span>
                                                    @else
                                                        <span class="text-xs text-zinc-500 font-semibold">Nonaktif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                @elseif($user->isWorkshop())
                    {{-- Workshop Owner: Workshop details --}}
                    <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                        <h3 class="text-base font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-3">Profil Bengkel</h3>
                        
                        @if(!$user->workshop)
                            <div class="text-center py-12 text-zinc-600 bg-zinc-950/20 border border-dashed border-zinc-800 rounded-xl">
                                <svg class="w-12 h-12 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                                <span class="text-xs">Pengguna ini belum melengkapi profil bengkel.</span>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-base font-bold text-zinc-150">{{ $user->workshop->name }}</h4>
                                    @if($user->workshop->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400">
                                            Terverifikasi
                                        </span>
                                    @elseif($user->workshop->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950/40 border border-red-900/50 text-red-400">
                                            Ditolak
                                        </span>
                                    @elseif($user->workshop->status === 'revision_needed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-950/40 border border-amber-900/50 text-amber-400">
                                            Revisi Diperlukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-950/40 border border-zinc-900/50 text-zinc-400">
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-[#2E3030]/50 pt-4 text-sm text-zinc-300">
                                    <div>
                                        <span class="text-xs text-zinc-500 block">No. Telepon Bengkel</span>
                                        <span class="font-semibold text-zinc-200">{{ $user->workshop->phone }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-zinc-500 block">Email Bengkel</span>
                                        <span class="font-semibold text-zinc-200">{{ $user->workshop->email }}</span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="text-xs text-zinc-500 block">Alamat Bengkel</span>
                                        <span class="font-semibold text-zinc-200">{{ $user->workshop->address }}, {{ $user->workshop->city }}, {{ $user->workshop->province }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-zinc-500 block">Jam Kerja</span>
                                        <span class="font-semibold text-zinc-200">{{ $user->workshop->operational_hours }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-zinc-500 block">Dokumen Legalitas</span>
                                        @if($user->workshop->legal_document_url)
                                            <a href="{{ asset('storage/' . $user->workshop->legal_document_url) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-400 hover:text-blue-300 mt-1">
                                                Buka Dokumen
                                            </a>
                                        @else
                                            <span class="text-zinc-600 text-xs italic mt-1 block">Tidak ada dokumen</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                @elseif($user->isSuperAdmin())
                    {{-- Super Admin brief --}}
                    <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg text-center py-12">
                        <div class="w-12 h-12 bg-red-950/20 text-red-400 border border-red-900/40 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-zinc-200 text-sm">Akun Hak Akses Penuh</h4>
                        <p class="text-xs text-zinc-500 max-w-sm mx-auto mt-2">Akun ini memiliki peran Super Admin. Akun ini memiliki izin penuh untuk mengelola konfigurasi platform, memverifikasi bengkel mitra, dan memantau seluruh data sistem Maintify.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
