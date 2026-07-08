<x-app-layout>
    @slot('pageTitle', 'Riwayat Service')
    @slot('breadcrumb', 'Kendaraan / Detail / Riwayat')

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- ── Back & Header ── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('vehicles.show', $vehicle) }}"
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 hover:border-zinc-500 transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Riwayat Service</h1>
                <p class="text-sm text-zinc-500 mt-0.5">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})</p>
            </div>
        </div>

        {{-- ── Statistics Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- Total Services --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-950/30 border border-red-900/40 flex items-center justify-center text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Frekuensi Service</p>
                    <p class="text-xl font-bold text-zinc-100 mt-0.5">{{ $frequency }} Kali</p>
                </div>
            </div>

            {{-- Avg Odometer Interval --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-955/30 border border-amber-900/40 flex items-center justify-center text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Rerata Jarak</p>
                    <p class="text-xl font-bold text-zinc-100 mt-0.5">
                        {{ $avgOdoInterval ? number_format($avgOdoInterval) . ' km' : 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Avg Time Interval --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-950/30 border border-emerald-900/40 flex items-center justify-center text-emerald-450">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Rerata Waktu</p>
                    <p class="text-xl font-bold text-zinc-100 mt-0.5">
                        {{ $avgDaysInterval ? $avgDaysInterval . ' Hari' : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Filters Section ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 mb-8">
            <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Filter Riwayat</h2>
            <form method="GET" action="{{ route('vehicles.service-history', $vehicle) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                {{-- Service Type --}}
                <div class="sm:col-span-2">
                    <label for="service_type" class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Jenis Service</label>
                    <select id="service_type"
                            name="service_type"
                            class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                        <option value="">Semua Jenis</option>
                        @foreach($serviceTypes as $key => $label)
                            <option value="{{ $key }}" {{ $filters['service_type'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           value="{{ $filters['start_date'] }}"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                </div>

                {{-- End Date --}}
                <div>
                    <label for="end_date" class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           value="{{ $filters['end_date'] }}"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                </div>

                {{-- Submit & Reset --}}
                <div class="sm:col-span-4 flex justify-end gap-3 mt-2 pt-4 border-t border-zinc-800">
                    @if($filters['service_type'] || $filters['start_date'] || $filters['end_date'])
                        <a href="{{ route('vehicles.service-history', $vehicle) }}"
                           class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-red-900/30 transition-all">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Timeline ── --}}
        <div class="relative pl-6 sm:pl-8 border-l border-zinc-850 space-y-8">
            @forelse($serviceRecords as $record)
                {{-- Dot indicator --}}
                <div class="absolute -left-[9px] mt-1.5 w-4 h-4 rounded-full border-4 border-[#121212] bg-red-500 shadow-md"></div>

                {{-- Timeline Card --}}
                <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 hover:border-zinc-500 transition-colors duration-300"
                     x-data="{ showParts: false }">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                        <div>
                            {{-- Date --}}
                            <p class="text-xs text-zinc-500 font-semibold">{{ $record->service_date->translatedFormat('d F Y') }}</p>
                            {{-- Type & Badge --}}
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-sm font-bold text-zinc-200">
                                    {{ $serviceTypes[$record->service_type] ?? $record->service_type }}
                                </span>
                                @if($record->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/30 border border-emerald-900/40 text-emerald-400">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-955/30 border border-amber-900/40 text-amber-505">
                                        Proses
                                    </span>
                                @endif
                            </div>
                            {{-- Workshop --}}
                            <p class="text-xs text-zinc-400 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>{{ $record->workshop?->name ?? 'Bengkel Mandiri / Umum' }}</span>
                            </p>
                        </div>
                        {{-- Cost & Odometer --}}
                        <div class="text-left sm:text-right flex-shrink-0">
                            <p class="text-sm font-bold text-red-400">Rp {{ number_format($record->total_cost, 0, ',', '.') }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">Odometer: {{ number_format($record->odometer_at_service) }} km</p>
                        </div>
                    </div>

                    {{-- Mechanic Notes --}}
                    @if($record->mechanic_notes)
                        <div class="bg-zinc-900/50 border border-zinc-850 rounded-xl p-3 text-xs text-zinc-400 leading-relaxed mb-3">
                            <span class="font-bold text-zinc-300 block mb-1">Catatan Mekanik:</span>
                            {{ $record->mechanic_notes }}
                        </div>
                    @endif

                    {{-- Spareparts details --}}
                    @if($record->parts->isNotEmpty())
                        <div class="border-t border-zinc-850 pt-3 mt-3">
                            <button type="button"
                                    @click="showParts = !showParts"
                                    class="flex items-center gap-1.5 text-xs font-semibold text-zinc-450 hover:text-zinc-200 transition-colors">
                                <span x-text="showParts ? 'Sembunyikan Sparepart' : 'Lihat Sparepart (' + {{ $record->parts->count() }} + ')'"></span>
                                <svg class="w-3.5 h-3.5 transform transition-transform duration-200"
                                     :class="showParts ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="showParts"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="mt-2.5 space-y-2">
                                @foreach($record->parts as $part)
                                    <div class="flex items-center justify-between text-xs py-1.5 px-3 bg-zinc-900/30 rounded-lg border border-zinc-850">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-zinc-300">{{ $part->part_name }}</span>
                                            @if($part->part_category)
                                                <span class="px-1.5 py-0.5 rounded bg-zinc-800 text-[10px] text-zinc-400 border border-zinc-700">
                                                    {{ $part->part_category }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-zinc-400 font-medium">
                                            {{ $part->quantity }} x Rp {{ number_format($part->unit_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-16 bg-[#181A1A] border border-[#2E3030] rounded-2xl -ml-6 sm:-ml-8">
                    <svg class="w-16 h-16 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-zinc-400 text-sm font-semibold">Belum Ada Riwayat Service</p>
                    <p class="text-zinc-650 text-xs mt-1">Belum ada service yang tercatat atau tidak sesuai filter Anda.</p>
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
