<x-app-layout>
    @slot('pageTitle', 'Vehicle Monitoring')
    @slot('breadcrumb', 'Admin / Vehicles')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Vehicle Monitoring</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Pantau data spesifikasi teknis dan riwayat pemeliharaan seluruh kendaraan di platform.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-400">
                Total: {{ $vehicles->total() }} Kendaraan
            </span>
        </div>

        {{-- Search Card --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('admin.vehicles.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari berdasarkan nomor plat, nomor rangka (VIN), atau nama pemilik..."
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
                    <a href="{{ route('admin.vehicles.index') }}"
                       class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Vehicles Table --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Plat Nomor</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Kendaraan</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Pemilik</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Odometer</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($vehicles as $vehicle)
                            <tr class="hover:bg-zinc-900/30 transition-colors align-middle">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-950/30 border border-red-900/40 text-red-400 font-mono">
                                        {{ $vehicle->plate_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-200">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                    <div class="text-[11px] text-zinc-500 mt-0.5">VIN: {{ $vehicle->chassis_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($vehicle->owner)
                                        <div class="text-sm font-semibold text-zinc-300">{{ $vehicle->owner->name }}</div>
                                        <div class="text-xs text-zinc-500 mt-0.5">{{ $vehicle->owner->email }}</div>
                                    @else
                                        <span class="text-zinc-650 text-xs italic">Tanpa Pemilik</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-zinc-350">
                                    {{ number_format($vehicle->current_odometer) }} Km
                                </td>
                                <td class="px-6 py-4">
                                    @if($vehicle->is_active)
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
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.vehicles.show', $vehicle->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-xs font-bold rounded-xl transition-all shadow-sm">
                                        Lihat Riwayat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Tidak Ada Kendaraan</p>
                                    <p class="text-zinc-650 text-xs mt-1">Tidak ada data kendaraan yang terdaftar atau cocok dengan pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($vehicles->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
