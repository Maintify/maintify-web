<x-app-layout>
    @slot('pageTitle', 'Laporan Operasional')
    @slot('breadcrumb', 'Workshop / Laporan')

    <div class="px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Laporan Operasional</h1>
                <p class="text-sm text-zinc-500 mt-0.5">{{ $report['period_label'] }}</p>
            </div>
            {{-- Export Button --}}
            <a href="{{ route('workshop.reports.export', ['start_date' => $report['start_date'], 'end_date' => $report['end_date']]) }}"
               id="btn-export-csv"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-red-900/30 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Unduh Laporan (CSV)
            </a>
        </div>

        {{-- ── Date Range Filter ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 mb-6 shadow-lg">
            <form method="GET" action="{{ route('workshop.reports.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="start_date" class="block text-xs font-medium text-zinc-400 mb-1.5">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ $report['start_date'] }}"
                           class="bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-red-500 transition-all">
                </div>
                <div>
                    <label for="end_date" class="block text-xs font-medium text-zinc-400 mb-1.5">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ $report['end_date'] }}"
                           class="bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-red-500 transition-all">
                </div>
                <button type="submit"
                        class="px-5 py-2 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 font-semibold rounded-xl transition-all text-sm">
                    Terapkan Filter
                </button>
                <a href="{{ route('workshop.reports.index') }}"
                   class="px-4 py-2 text-zinc-500 hover:text-zinc-300 text-sm transition-colors">
                    Reset
                </a>
            </form>
        </div>

        {{-- ── Summary Metric Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- Total Services --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 shadow-lg">
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Total Servis</p>
                <p class="text-4xl font-bold text-zinc-100">{{ number_format($report['total_services']) }}</p>
            </div>
            {{-- Total Revenue --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 shadow-lg">
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Total Pendapatan</p>
                <p class="text-3xl font-bold text-emerald-400">{{ $report['total_revenue_formatted'] }}</p>
            </div>
            {{-- Avg Revenue --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 shadow-lg">
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Rata-rata / Servis</p>
                <p class="text-3xl font-bold text-zinc-300">{{ $report['avg_revenue_formatted'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            {{-- ── Breakdown per Type ── --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 shadow-lg">
                <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Breakdown per Jenis Servis</h2>
                @if($report['by_type']->isEmpty())
                    <p class="text-zinc-600 text-sm text-center py-8">Tidak ada data dalam periode ini.</p>
                @else
                    <div class="space-y-3">
                        @foreach($report['by_type'] as $row)
                            @php
                                $pct = $report['total_services'] > 0
                                    ? round(($row['count'] / $report['total_services']) * 100)
                                    : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-zinc-300">{{ $row['label'] }}</span>
                                    <span class="text-sm font-semibold text-zinc-100">{{ $row['count'] }} <span class="text-zinc-500 font-normal text-xs">({{ $pct }}%)</span></span>
                                </div>
                                <div class="w-full bg-zinc-800 rounded-full h-1.5">
                                    <div class="bg-red-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="text-right text-xs text-zinc-500 mt-0.5">{{ $row['revenue_formatted'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Top 10 Spareparts ── --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 shadow-lg">
                <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Top 10 Sparepart Digunakan</h2>
                @if($report['top_parts']->isEmpty())
                    <p class="text-zinc-600 text-sm text-center py-8">Tidak ada data sparepart dalam periode ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-zinc-500 text-left border-b border-zinc-800">
                                    <th class="pb-2 font-medium">#</th>
                                    <th class="pb-2 font-medium">Nama Sparepart</th>
                                    <th class="pb-2 font-medium text-right">Qty</th>
                                    <th class="pb-2 font-medium text-right">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['top_parts'] as $i => $part)
                                    <tr class="border-b border-zinc-800/50 hover:bg-zinc-800/20 transition-colors">
                                        <td class="py-2.5 text-zinc-600 pr-3">{{ $i + 1 }}</td>
                                        <td class="py-2.5 text-zinc-200 font-medium">{{ $part->part_name }}</td>
                                        <td class="py-2.5 text-zinc-300 text-right">{{ number_format($part->total_qty) }}</td>
                                        <td class="py-2.5 text-emerald-400 text-right">Rp {{ number_format($part->total_value, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Daily Timeline Table ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-5 shadow-lg">
            <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Detail Servis Per Hari</h2>
            @if($report['daily']->isEmpty())
                <div class="text-center py-10">
                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-zinc-600">Tidak ada data servis dalam periode yang dipilih.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-zinc-500 text-left border-b border-zinc-800">
                                <th class="pb-3 font-medium">Tanggal</th>
                                <th class="pb-3 font-medium text-center">Jumlah Servis</th>
                                <th class="pb-3 font-medium text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['daily'] as $day)
                                <tr class="border-b border-zinc-800/50 hover:bg-zinc-800/20 transition-colors">
                                    <td class="py-3 text-zinc-300">
                                        {{ \Carbon\Carbon::parse($day['date'])->translatedFormat('D, d M Y') }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-zinc-800 text-zinc-200 font-bold text-xs">
                                            {{ $day['count'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right text-emerald-400 font-medium">{{ $day['revenue_formatted'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-zinc-700">
                                <td class="pt-3 text-zinc-400 font-semibold">TOTAL</td>
                                <td class="pt-3 text-center text-zinc-100 font-bold">{{ number_format($report['total_services']) }}</td>
                                <td class="pt-3 text-right text-emerald-300 font-bold">{{ $report['total_revenue_formatted'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
