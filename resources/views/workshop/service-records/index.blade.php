<x-app-layout>
    @slot('pageTitle', 'Service Records')
    @slot('breadcrumb', 'Workshop / Service Records')

    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Service Records</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Riwayat seluruh service yang dicatat oleh bengkel Anda</p>
            </div>
            <a href="{{ route('workshop.scan') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v6a2 2 0 002 2h2m2-12V4a2 2 0 012-2h4a2 2 0 012 2v1"/>
                </svg>
                Scan QR untuk Service Baru
            </a>
        </div>

        {{-- ── Search & Filter ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('workshop.service-records.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari berdasarkan plat nomor, merek, atau model..."
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <select name="type"
                        class="bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <option value="">Semua Jenis Service</option>
                    @foreach($serviceTypes as $value => $label)
                        <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all">
                    Cari
                </button>
                @if($search || $type)
                    <a href="{{ route('workshop.service-records.index') }}"
                       class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- ── Service Records Table ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Kendaraan</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Jenis Service</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Odometer</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Biaya</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($serviceRecords as $record)
                            <tr class="hover:bg-zinc-900/30 transition-colors">
                                {{-- Date --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="text-sm font-medium text-zinc-300">
                                        {{ $record->service_date->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-xs text-zinc-500 mt-0.5">
                                        {{ $record->service_date->diffForHumans() }}
                                    </div>
                                </td>
                                {{-- Vehicle --}}
                                <td class="px-6 py-4 align-top">
                                    @if($record->vehicle)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-950/30 border border-red-900/40 text-red-400">
                                                {{ $record->vehicle->plate_number }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-zinc-400 mt-1">
                                            {{ $record->vehicle->brand }} {{ $record->vehicle->model }}
                                        </div>
                                        @if($record->vehicle->owner)
                                            <div class="text-[11px] text-zinc-600 mt-0.5">{{ $record->vehicle->owner->name }}</div>
                                        @endif
                                    @else
                                        <span class="text-zinc-600 text-xs italic">Kendaraan dihapus</span>
                                    @endif
                                </td>
                                {{-- Service Type --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="text-sm text-zinc-300">{{ $record->service_type_label_readable }}</div>
                                    @if($record->status === \App\Models\ServiceRecord::STATUS_IN_PROGRESS)
                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-950/30 border border-amber-900/40 text-amber-400">
                                            Sedang Dikerjakan
                                        </span>
                                    @endif
                                </td>
                                {{-- Odometer --}}
                                <td class="px-6 py-4 align-top">
                                    <span class="text-sm text-zinc-400">{{ number_format($record->odometer_at_service) }} km</span>
                                </td>
                                {{-- Cost --}}
                                <td class="px-6 py-4 align-top">
                                    <span class="text-sm font-semibold text-zinc-200">{{ $record->formatted_cost }}</span>
                                </td>
                                {{-- Actions --}}
                                <td class="px-6 py-4 align-top text-right">
                                    @if($record->created_at->addHours($editLimitHours)->isFuture())
                                        <a href="{{ route('workshop.service-records.edit', $record) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-xs font-semibold rounded-lg transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Ubah
                                        </a>
                                    @else
                                        <span class="text-[11px] text-zinc-600 italic">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Belum Ada Service Record</p>
                                    <p class="text-zinc-650 text-xs mt-1">Scan QR Code kendaraan untuk mulai mencatat service.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ── --}}
            @if($serviceRecords->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $serviceRecords->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
