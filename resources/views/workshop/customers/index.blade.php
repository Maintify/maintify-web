<x-app-layout>
    @slot('pageTitle', 'Daftar Pelanggan')
    @slot('breadcrumb', 'Workshop / Pelanggan')

    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Daftar Pelanggan</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Daftar pelanggan (pemilik kendaraan) yang pernah melakukan service di bengkel Anda</p>
            </div>
        </div>

        {{-- ── Search & Filter ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('workshop.customers.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari berdasarkan nama pelanggan atau plat nomor..."
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
                    <a href="{{ route('workshop.customers.index') }}"
                       class="px-4 py-2.5 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- ── Customers Table ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Nama Pelanggan</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Kendaraan</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Service Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-zinc-900/30 transition-colors">
                                {{-- Name & Contact --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="font-bold text-zinc-200">{{ $customer->name }}</div>
                                    <div class="text-xs text-zinc-500 mt-1 flex flex-col gap-0.5">
                                        <span>Email: <span class="text-zinc-450">{{ $customer->email }}</span></span>
                                        @if($customer->phone)
                                            <span>No. Hp: <span class="text-zinc-450">{{ $customer->phone }}</span></span>
                                        @endif
                                    </div>
                                </td>
                                {{-- Vehicles --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="space-y-3">
                                        @foreach($customer->vehicles as $vehicle)
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-x-3 gap-y-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-950/30 border border-red-900/40 text-red-400">
                                                    {{ $vehicle->plate_number }}
                                                </span>
                                                <span class="text-xs font-semibold text-zinc-300">
                                                    {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})
                                                </span>
                                                @php
                                                    $latestRecord = $vehicle->serviceRecords->first();
                                                @endphp
                                                @if($latestRecord)
                                                    <span class="text-[10px] text-zinc-500 italic">
                                                        (Odo: {{ number_format($latestRecord->odometer_at_service) }} km)
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                {{-- Last Service Date --}}
                                <td class="px-6 py-4 align-top">
                                    @if($customer->last_service_date)
                                        <div class="text-sm font-medium text-zinc-300">
                                            {{ \Carbon\Carbon::parse($customer->last_service_date)->translatedFormat('d F Y') }}
                                        </div>
                                        <div class="text-xs text-zinc-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($customer->last_service_date)->diffForHumans() }}
                                        </div>
                                    @else
                                        <span class="text-zinc-600 text-xs italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Belum Ada Pelanggan</p>
                                    <p class="text-zinc-650 text-xs mt-1">Bengkel Anda belum pernah melayani pendaftaran/service kendaraan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ── --}}
            @if($customers->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
