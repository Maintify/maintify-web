<x-app-layout>
    @slot('pageTitle', 'Detail Kendaraan - ' . $vehicle->plate_number)
    @slot('breadcrumb', 'Admin / Vehicles / Detail')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Back Button & Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Pemantauan
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Detail Kendaraan</h1>
                    <p class="text-sm text-zinc-500 mt-0.5">Spesifikasi detail teknis, status kesehatan, dan histori pemeliharaan lengkap (Read-Only).</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-950/30 border border-red-900/40 text-red-400 font-mono">
                        {{ $vehicle->plate_number }}
                    </span>
                    @if($vehicle->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-500">
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Two-Column Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Identity & Specs --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Vehicle Info Card --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
                    {{-- Photo --}}
                    <div class="relative w-100 h-48 bg-zinc-950 flex items-center justify-center border-b border-[#2E3030]">
                        @if($vehicle->photo_url)
                            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->brand }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-16 h-16 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M8 9h2v2H8V9zm0-4h2v2H8V5zm0 8h2v2H8v-2zM20 9h2v2h-2V9zm0-4h2v2h-2V5zm0 8h2v2h-2v-2z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Specs --}}
                    <div class="p-6 space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-zinc-200">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                            <span class="text-xs text-zinc-500 block mt-1">Tahun Produksi: {{ $vehicle->year }}</span>
                        </div>

                        <div class="space-y-2 border-t border-[#2E3030] pt-4 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-[#2E3030]/30">
                                <span class="text-zinc-500">Nomor Rangka</span>
                                <span class="font-semibold text-zinc-250 font-mono">{{ $vehicle->chassis_number ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-[#2E3030]/30">
                                <span class="text-zinc-500">Nomor Mesin</span>
                                <span class="font-semibold text-zinc-250 font-mono">{{ $vehicle->engine_number ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-[#2E3030]/30">
                                <span class="text-zinc-500">Bahan Bakar</span>
                                <span class="font-semibold text-zinc-250">
                                    {{ $vehicle->fuel_type === 'gasoline' ? 'Bensin' : ($vehicle->fuel_type === 'diesel' ? 'Diesel' : ($vehicle->fuel_type === 'electric' ? 'Listrik' : ucfirst($vehicle->fuel_type ?? '-'))) }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <span class="text-zinc-500">Warna</span>
                                <span class="font-semibold text-zinc-250">{{ $vehicle->color ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Health & Stats --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg space-y-4">
                    <h3 class="text-sm font-bold text-zinc-200 border-b border-[#2E3030] pb-2">Status Kesehatan</h3>
                    
                    {{-- Odometer --}}
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500">Odometer Saat Ini</span>
                        <span class="font-bold text-zinc-200">{{ number_format($vehicle->current_odometer) }} Km</span>
                    </div>

                    {{-- Engine Health Score --}}
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-zinc-500">Engine Health Score</span>
                            <span class="font-semibold text-zinc-200">{{ $vehicle->health_score }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-950 rounded-full overflow-hidden">
                            @php
                                $healthColor = $vehicle->health_score >= 80 ? 'bg-emerald-500' : ($vehicle->health_score >= 50 ? 'bg-amber-500' : 'bg-red-500');
                            @endphp
                            <div class="h-full rounded-full {{ $healthColor }}" style="width: {{ $vehicle->health_score }}%;"></div>
                        </div>
                    </div>

                    {{-- Oil Life --}}
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-zinc-500">Sisa Umur Oli (Oil Life)</span>
                            <span class="font-semibold text-zinc-200">{{ $vehicle->oil_life_percentage ?? 100 }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-zinc-950 rounded-full overflow-hidden">
                            @php
                                $oilScore = $vehicle->oil_life_percentage ?? 100;
                                $oilColor = $oilScore >= 40 ? 'bg-emerald-500' : ($oilScore >= 15 ? 'bg-amber-500' : 'bg-red-500');
                            @endphp
                            <div class="h-full rounded-full {{ $oilColor }}" style="width: {{ $oilScore }}%;"></div>
                        </div>
                    </div>
                </div>

                {{-- Owner Card --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-zinc-200 mb-3 border-b border-[#2E3030] pb-2">Informasi Pemilik</h3>
                    @if($vehicle->owner)
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-xs text-zinc-500 block">Nama Pemilik</span>
                                <span class="font-semibold text-zinc-200">{{ $vehicle->owner->name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block">Email Pemilik</span>
                                <span class="font-semibold text-zinc-200">{{ $vehicle->owner->email }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block">No. Telepon</span>
                                <span class="font-semibold text-zinc-200">{{ $vehicle->owner->phone_number ?? '-' }}</span>
                            </div>
                        </div>
                    @else
                        <span class="text-zinc-650 text-xs italic">Tidak terikat pemilik manapun saat ini.</span>
                    @endif
                </div>
            </div>

            {{-- Right Column: Service History Timeline --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Service Summary statistics --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-sm font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-2">Ringkasan Pemeliharaan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div class="p-4 bg-zinc-900/50 rounded-xl border border-[#2E3030]/50">
                            <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-wider">Total Servis</span>
                            <span class="text-lg font-bold text-zinc-100 mt-1 block">{{ $totalServices }}</span>
                        </div>
                        <div class="p-4 bg-zinc-900/50 rounded-xl border border-[#2E3030]/50">
                            <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-wider">Total Pengeluaran</span>
                            <span class="text-lg font-bold text-zinc-100 mt-1 block">Rp {{ number_format($totalCost) }}</span>
                        </div>
                        <div class="p-4 bg-zinc-900/50 rounded-xl border border-[#2E3030]/50">
                            <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-wider">Rata-Rata Interval</span>
                            <span class="text-lg font-bold text-zinc-100 mt-1 block">{{ $avgInterval ? $avgInterval . ' Hari' : '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                    <h3 class="text-base font-bold text-zinc-200 mb-6 border-b border-[#2E3030] pb-3">Histori Pemeliharaan Lengkap</h3>
                    
                    @if($serviceRecords->isEmpty())
                        <div class="text-center py-12 text-zinc-600 bg-zinc-950/20 border border-dashed border-zinc-800 rounded-xl">
                            <svg class="w-12 h-12 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            <span class="text-xs">Belum ada catatan histori service untuk kendaraan ini.</span>
                        </div>
                    @else
                        <div class="relative border-l border-zinc-800 ml-4 pl-6 space-y-8">
                            @foreach($serviceRecords as $record)
                                <div class="relative">
                                    {{-- Timeline node --}}
                                    <span class="absolute -left-[31px] top-1.5 w-4.5 h-4.5 rounded-full border border-[#2E3030] bg-[#1d1f1f] flex items-center justify-center text-zinc-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    </span>

                                    <div class="bg-zinc-900/40 border border-[#2E3030]/50 rounded-xl p-5 hover:border-zinc-700/50 transition-colors">
                                        {{-- Title / Type --}}
                                        <div class="flex justify-between items-start gap-4 flex-wrap mb-2">
                                            <div>
                                                @php
                                                    $serviceTypes = \App\Models\ServiceRecord::SERVICE_TYPES;
                                                    $serviceLabel = $serviceTypes[$record->service_type] ?? ucfirst($record->service_type);
                                                @endphp
                                                <h4 class="text-sm font-bold text-zinc-200">{{ $serviceLabel }}</h4>
                                                <span class="text-xs text-zinc-500 block mt-0.5">{{ $record->service_date->translatedFormat('d F Y') }} &middot; {{ $record->workshop->name }}</span>
                                            </div>
                                            <span class="text-sm font-bold text-zinc-100">Rp {{ number_format($record->total_cost) }}</span>
                                        </div>

                                        {{-- Stats details --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-3 text-xs bg-zinc-950/40 p-3 rounded-lg border border-zinc-900">
                                            <div>
                                                <span class="text-zinc-500 block">Odometer Service</span>
                                                <span class="font-semibold text-zinc-300">{{ number_format($record->odometer_at_service) }} Km</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-500 block">Mekanik</span>
                                                <span class="font-semibold text-zinc-300">{{ $record->performedBy->name ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- Spareparts replaced --}}
                                        @if($record->parts && $record->parts->isNotEmpty())
                                            <div class="mt-3">
                                                <span class="text-xs font-semibold text-zinc-400 block mb-1">Suku Cadang Diganti:</span>
                                                <ul class="space-y-1">
                                                    @foreach($record->parts as $part)
                                                        <li class="flex justify-between items-center text-xs text-zinc-300">
                                                            <span>&bull; {{ $part->part_name }} (x{{ $part->quantity }})</span>
                                                            <span class="text-zinc-400">Rp {{ number_format($part->unit_price * $part->quantity) }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- Notes --}}
                                        @if($record->mechanic_notes)
                                            <div class="mt-3 text-xs border-t border-[#2E3030]/30 pt-3 text-zinc-400">
                                                <span class="text-zinc-500 block font-semibold mb-1">Catatan Tambahan:</span>
                                                {{ $record->mechanic_notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
