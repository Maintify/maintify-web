<x-app-layout>
    @slot('pageTitle', 'Riwayat Servis')
    @slot('breadcrumb', 'Kendaraan / Riwayat Servis')

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('vehicles.index') }}"
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 hover:border-zinc-500 transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Riwayat Servis Kendaraan</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Daftar ringkas seluruh riwayat pemeliharaan kendaraan Anda</p>
            </div>
        </div>

        {{-- ── Statistics Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- Total Services --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-950/30 border border-red-900/40 flex items-center justify-center text-red-400 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Total Servis</p>
                    <p class="text-xl font-bold text-zinc-100 mt-0.5">{{ $frequency }} Kali</p>
                </div>
            </div>

            {{-- Total Cost --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-950/30 border border-emerald-900/40 flex items-center justify-center text-emerald-400 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Total Pengeluaran</p>
                    <p class="text-xl font-bold text-emerald-400 mt-0.5">Rp {{ number_format($totalCostAll ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Avg Odometer Interval --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-955/30 border border-amber-900/40 flex items-center justify-center text-amber-500 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Rerata Interval</p>
                    <p class="text-xl font-bold text-zinc-100 mt-0.5">
                        {{ $avgOdoInterval ? number_format($avgOdoInterval) . ' km' : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Filters Section ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 mb-8">
            <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Filter Riwayat</h2>
            <form method="GET" action="{{ route('vehicles.service-history.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                {{-- Vehicle Filter --}}
                <div class="sm:col-span-2">
                    <label for="vehicle_id" class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Pilih Kendaraan</label>
                    <select id="vehicle_id"
                            name="vehicle_id"
                            class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                        <option value="">Semua Kendaraan</option>
                        @foreach($userVehicles as $v)
                            <option value="{{ $v->id }}" {{ (string)($filters['vehicle_id'] ?? '') === (string)$v->id ? 'selected' : '' }}>
                                {{ $v->brand }} {{ $v->model }} ({{ $v->plate_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           value="{{ $filters['start_date'] ?? '' }}"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                </div>

                {{-- End Date --}}
                <div>
                    <label for="end_date" class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           value="{{ $filters['end_date'] ?? '' }}"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                </div>

                {{-- Submit & Reset --}}
                <div class="sm:col-span-4 flex justify-end gap-3 mt-2 pt-4 border-t border-zinc-800">
                    @if(!empty($filters['vehicle_id']) || !empty($filters['service_type']) || !empty($filters['start_date']) || !empty($filters['end_date']))
                        <a href="{{ route('vehicles.service-history.index') }}"
                           class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-red-900/30 transition-all">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Point-based History List ── --}}
        <div class="space-y-4">
            @forelse($serviceRecords as $record)
                <div class="bg-[#181A1A] border border-[#2E3030] hover:border-zinc-600 rounded-2xl p-5 transition-colors duration-200">
                    {{-- Header Row: Date & Status --}}
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-zinc-800 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $record->status === 'completed' ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                            <span class="text-xs font-semibold text-zinc-400">
                                {{ $record->service_date?->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div>
                            @if($record->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400">
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-955/40 border border-amber-900/50 text-amber-400">
                                    Dalam Proses
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Point-based summary --}}
                    <ul class="space-y-2 text-sm text-zinc-300 mb-4">
                        <li class="flex items-start gap-2.5">
                            <span class="text-zinc-500 font-bold">•</span>
                            <span class="flex-1">
                                <strong class="text-zinc-400 font-medium">Kendaraan:</strong>
                                <span class="font-bold text-zinc-100 ml-1">
                                    {{ $record->vehicle?->brand }} {{ $record->vehicle?->model }}
                                </span>
                                <span class="text-xs text-red-400 bg-red-950/30 border border-red-900/40 px-2 py-0.5 rounded ml-1.5">
                                    {{ $record->vehicle?->plate_number }}
                                </span>
                            </span>
                        </li>

                        <li class="flex items-start gap-2.5">
                            <span class="text-zinc-500 font-bold">•</span>
                            <span class="flex-1">
                                <strong class="text-zinc-400 font-medium">Jenis Servis:</strong>
                                <span class="font-semibold text-zinc-200 ml-1">
                                    {{ $serviceTypes[$record->service_type] ?? $record->service_type }}
                                </span>
                            </span>
                        </li>

                        <li class="flex items-start gap-2.5">
                            <span class="text-zinc-500 font-bold">•</span>
                            <span class="flex-1">
                                <strong class="text-zinc-400 font-medium">Bengkel:</strong>
                                <span class="text-zinc-300 ml-1">
                                    {{ $record->workshop?->name ?? 'Bengkel Mandiri / Umum' }}
                                </span>
                            </span>
                        </li>

                        <li class="flex items-start gap-2.5">
                            <span class="text-zinc-500 font-bold">•</span>
                            <span class="flex-1">
                                <strong class="text-zinc-400 font-medium">Odometer:</strong>
                                <span class="text-zinc-300 ml-1">
                                    {{ number_format($record->odometer_at_service) }} km
                                </span>
                            </span>
                        </li>

                        <li class="flex items-start gap-2.5">
                            <span class="text-zinc-500 font-bold">•</span>
                            <span class="flex-1">
                                <strong class="text-zinc-400 font-medium">Total Biaya:</strong>
                                <span class="font-bold text-emerald-400 ml-1">
                                    Rp {{ number_format($record->total_cost, 0, ',', '.') }}
                                </span>
                            </span>
                        </li>

                        @if($record->parts->isNotEmpty())
                            <li class="flex items-start gap-2.5">
                                <span class="text-zinc-500 font-bold">•</span>
                                <span class="flex-1">
                                    <strong class="text-zinc-400 font-medium">Sparepart:</strong>
                                    <span class="text-zinc-400 ml-1 text-xs">
                                        {{ $record->parts->pluck('part_name')->join(', ') }}
                                    </span>
                                </span>
                            </li>
                        @endif
                    </ul>

                    {{-- Action Footer --}}
                    @if($record->vehicle)
                        <div class="pt-3 border-t border-zinc-800/80 flex justify-end">
                            <a href="{{ route('vehicles.show', $record->vehicle) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-xs font-semibold text-zinc-300 hover:text-zinc-100 transition-all">
                                <span>Lihat Detail Kendaraan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-16 bg-[#181A1A] border border-[#2E3030] rounded-2xl">
                    <svg class="w-14 h-14 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-zinc-400 text-sm font-semibold">Belum Ada Riwayat Servis</p>
                    <p class="text-zinc-600 text-xs mt-1">Belum ada servis yang tercatat untuk kendaraan Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- ── Pagination ── --}}
        @if($serviceRecords->hasPages())
            <div class="mt-8">
                {{ $serviceRecords->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
