{{-- ============================================================
     Workshop Admin Dashboard Partial
     Variables: $workshop, $totalServices, $dailyServices,
                $weeklyServices, $monthlyServices, $activeStaffCount,
                $chartLabels, $chartValues, $topSpareparts,
                $activeCustomers, $recentServices
     ============================================================ --}}

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <h1 class="page-title">Dashboard Bengkel</h1>
            <span class="badge badge-success" style="padding: 2px 8px; font-size: 11px;">
                <svg style="width: 10px; height: 10px; display: inline; margin-right: 2px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.9L10 1.154l7.834 3.746a1 1 0 01.586.904v4.305c0 4.11-2.266 7.9-5.918 9.8l-.502.261a1 1 0 01-.998 0l-.502-.261C6.834 18.009 4.568 14.22 4.568 10.11V5.804a1 1 0 01.598-.904zM10 10.02a1 1 0 100-2 1 1 0 000 2zm-1 4a1 1 0 102 0 1 1 0 00-2 0z" clip-rule="evenodd"/>
                </svg>
                Mitra Terverifikasi
            </span>
        </div>
        <p class="page-subtitle">{{ $workshop->name }} &middot; {{ $workshop->city }}</p>
    </div>
</div>

{{-- ============================================================
     KPI Stats Grid — 4 cols desktop / 2 cols tablet / 1 col mobile
     ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Service Hari Ini --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(59,130,246,0.1);color:#60a5fa;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $dailyServices }}</div>
        <div class="stat-card-label">Service Hari Ini</div>
    </div>

    {{-- Service Minggu Ini --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(34,197,94,0.1);color:#4ade80;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $weeklyServices }}</div>
        <div class="stat-card-label">Service Minggu Ini</div>
    </div>

    {{-- Service Bulan Ini --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(168,85,247,0.1);color:#c084fc;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $monthlyServices }}</div>
        <div class="stat-card-label">Service Bulan Ini</div>
    </div>

    {{-- Staff Aktif --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(245,158,11,0.1);color:#fbbf24;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $activeStaffCount }}</div>
        <div class="stat-card-label">Staf Aktif</div>
    </div>

</div>

{{-- ============================================================
     Main Dashboard Layout Grid
     ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left Section (Charts) - Takes 2 cols --}}
    <div class="lg:col-span-2 flex flex-col gap-6">

        {{-- Trending Chart Card --}}
        <div class="card h-full">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Tren Kendaraan Dilayani</h3>
                    <p class="card-subtitle">Statistik kendaraan diservis 7 hari terakhir</p>
                </div>
                <span class="badge badge-primary">Real-Time</span>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="servicesChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Right Section (Recent Services) - Takes 1 col --}}
    <div class="flex flex-col gap-6">

        {{-- Recent Services Card --}}
        <div class="card h-full">
            <div class="section-title">
                <h3>Sesi Service Terbaru</h3>
            </div>

            @if($recentServices->isEmpty())
                <div class="empty-state" style="padding:40px 0;">
                    <div class="empty-state-icon">
                        <svg style="width:28px;height:28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title">Belum ada aktivitas service</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach($recentServices as $service)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--color-border);' : '' }}">

                            {{-- Left: vehicle info + owner --}}
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:600;color:#F4F4F5;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $service->vehicle->brand }} {{ $service->vehicle->model }}
                                </p>
                                <p style="font-size:11px;color:#71717A;margin:2px 0 0;">
                                    {{ $service->vehicle->plate_number }}
                                    @if($service->vehicle->owner)
                                        · {{ $service->vehicle->owner->name }}
                                    @endif
                                </p>
                            </div>

                            {{-- Right: cost & date --}}
                            <div style="flex-shrink:0;text-align:right;">
                                <span style="font-size:12px;font-weight:600;color:#F4F4F5;display:block;">
                                    {{ $service->formatted_cost ?: '—' }}
                                </span>
                                <span style="font-size:10px;color:#71717A;">
                                    {{ $service->service_date->format('d M') }}
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>

{{-- ============================================================
     Inventory Analysis Section (Fast Moving, Slow Moving, Dead Stock)
     ============================================================ --}}
<div class="mt-8" x-data="{ showModal: false, activePart: null }">
    <div class="flex items-center justify-between mb-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-zinc-100">Analisis Inventaris Sparepart</h2>
                <span class="px-2 py-0.5 text-[11px] font-semibold bg-zinc-800 text-zinc-300 rounded-full border border-zinc-700">Live Analytics</span>
            </div>
            <p class="text-xs text-zinc-500 mt-0.5">Klasifikasi performa pergerakan stok berdasarkan volume penggunaan servis.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- CARD 1: FAST MOVING (BEST SELLER) --}}
        <div class="bg-[#181A1A] border border-emerald-900/40 rounded-2xl p-5 shadow-lg relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>

            <div>
                {{-- Header with Individual Period Filter --}}
                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-zinc-800/80">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-950/60 border border-emerald-800/50 flex items-center justify-center text-emerald-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-100">Fast Moving</h3>
                            <p class="text-[11px] text-zinc-500">Best Seller</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center">
                        <input type="hidden" name="period_slow" value="{{ $periodSlow }}">
                        <input type="hidden" name="period_dead" value="{{ $periodDead }}">
                        <select name="period_fast" onchange="this.form.submit()"
                                class="bg-[#121414] border border-[#2E3030] text-emerald-400 text-[11px] font-semibold rounded-lg px-6 py-1 focus:outline-none focus:border-emerald-500 cursor-pointer">
                            <option value="7" {{ $periodFast === '7' ? 'selected' : '' }}>7 Hari</option>
                            <option value="30" {{ $periodFast === '30' ? 'selected' : '' }}>30 Hari</option>
                            <option value="365" {{ $periodFast === '365' ? 'selected' : '' }}>1 Tahun</option>
                        </select>
                    </form>
                </div>

                @if($fastMovingParts->isEmpty())
                    <div class="py-8 text-center border border-dashed border-zinc-800 rounded-xl my-2">
                        <svg class="w-8 h-8 text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-xs text-zinc-500 font-medium">Belum ada sparepart fast moving</p>
                        <p class="text-[11px] text-zinc-650 mt-0.5">Belum ada item dengan volume transaksi tinggi di periode ini.</p>
                    </div>
                @else
                    <div class="space-y-3 my-2">
                        @foreach($fastMovingParts as $part)
                            <div @click="activePart = {
                                    name: '{{ addslashes($part->part_name) }}',
                                    category: '{{ addslashes($part->part_category ?? 'Suku Cadang') }}',
                                    type: 'fast',
                                    typeName: 'Fast Moving',
                                    qty: '{{ $part->total_quantity }} unit',
                                    remainingStock: '{{ $part->current_stock }} unit',
                                    revenue: 'Rp {{ number_format((float) $part->total_revenue, 0, ',', '.') }}',
                                    price: '{{ $part->unit_price > 0 ? 'Rp ' . number_format($part->unit_price, 0, ',', '.') : 'Terhitung dari transaksi' }}',
                                    lastUsed: 'Dalam {{ $periodFast }} hari terakhir',
                                    recommendation: 'Stok bergerak sangat cepat! Rekomendasikan re-stock minimal 15-20 unit agar tidak kehabisan persediaan.'
                                 }; showModal = true"
                                 class="p-3 bg-zinc-900/60 border border-zinc-800/80 rounded-xl hover:border-emerald-700/60 hover:bg-zinc-850/80 hover:scale-[1.01] transition-all cursor-pointer"
                                 title="Klik untuk melihat detail produk">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-zinc-200 truncate">{{ $part->part_name }}</p>
                                        <p class="text-[11px] text-zinc-500 mt-0.5">{{ $part->part_category ?? 'Suku Cadang' }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-950/70 border border-emerald-900/50 text-emerald-400 text-xs font-extrabold flex-shrink-0">
                                        {{ $part->total_quantity }} unit
                                    </span>
                                </div>
                                <div class="mt-2 pt-2 border-t border-zinc-850/80 flex items-center justify-between text-[11px]">
                                    <span class="text-zinc-500">Omset Perkiraan:</span>
                                    <span class="font-semibold text-emerald-300">Rp {{ number_format((float) $part->total_revenue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-3 pt-3 border-t border-zinc-800/60 text-[11px] text-zinc-500 flex items-center justify-between">
                <span>Rekomendasi:</span>
                <span class="text-emerald-400 font-medium">Prioritaskan Re-stock Stok</span>
            </div>
        </div>

        {{-- CARD 2: SLOW MOVING --}}
        <div class="bg-[#181A1A] border border-amber-900/40 rounded-2xl p-5 shadow-lg relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-400"></div>

            <div>
                {{-- Header with Individual Period Filter --}}
                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-zinc-800/80">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-950/60 border border-amber-800/50 flex items-center justify-center text-amber-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-100">Slow Moving</h3>
                            <p class="text-[11px] text-zinc-500">Pergerakan Lambat</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center">
                        <input type="hidden" name="period_fast" value="{{ $periodFast }}">
                        <input type="hidden" name="period_dead" value="{{ $periodDead }}">
                        <select name="period_slow" onchange="this.form.submit()"
                                class="bg-[#121414] border border-[#2E3030] text-amber-400 text-[11px] font-semibold rounded-lg px-6 py-1 focus:outline-none focus:border-amber-500 cursor-pointer">
                            <option value="7" {{ $periodSlow === '7' ? 'selected' : '' }}>7 Hari</option>
                            <option value="30" {{ $periodSlow === '30' ? 'selected' : '' }}>30 Hari</option>
                            <option value="365" {{ $periodSlow === '365' ? 'selected' : '' }}>1 Tahun</option>
                        </select>
                    </form>
                </div>

                @if($slowMovingParts->isEmpty())
                    <div class="py-8 text-center border border-dashed border-zinc-800 rounded-xl my-2">
                        <svg class="w-8 h-8 text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-xs text-zinc-500 font-medium">Tidak ada sparepart slow moving</p>
                        <p class="text-[11px] text-zinc-650 mt-0.5">Semua item terpakai memiliki ritme penjualan yang stabil.</p>
                    </div>
                @else
                    <div class="space-y-3 my-2">
                        @foreach($slowMovingParts as $part)
                            <div @click="activePart = {
                                    name: '{{ addslashes($part->part_name) }}',
                                    category: '{{ addslashes($part->part_category ?? 'Suku Cadang') }}',
                                    type: 'slow',
                                    typeName: 'Slow Moving',
                                    qty: '{{ $part->total_quantity }} unit',
                                    remainingStock: '{{ $part->current_stock }} unit',
                                    revenue: 'Pergerakan Lambat',
                                    price: '{{ $part->unit_price > 0 ? 'Rp ' . number_format($part->unit_price, 0, ',', '.') : 'Terhitung dari transaksi' }}',
                                    lastUsed: '{{ $part->last_used_date ? \Carbon\Carbon::parse($part->last_used_date)->translatedFormat('d M Y') : '-' }}',
                                    recommendation: 'Pergerakan stok lambat. Batasi pembelian stok baru sampai persediaan saat ini mendekati habis.'
                                 }; showModal = true"
                                 class="p-3 bg-zinc-900/60 border border-zinc-800/80 rounded-xl hover:border-amber-700/60 hover:bg-zinc-850/80 hover:scale-[1.01] transition-all cursor-pointer"
                                 title="Klik untuk melihat detail produk">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-zinc-200 truncate">{{ $part->part_name }}</p>
                                        <p class="text-[11px] text-zinc-500 mt-0.5">{{ $part->part_category ?? 'Suku Cadang' }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-lg bg-amber-950/70 border border-amber-900/50 text-amber-400 text-xs font-bold flex-shrink-0">
                                        {{ $part->total_quantity }} unit
                                    </span>
                                </div>
                                <div class="mt-2 pt-2 border-t border-zinc-850/80 flex items-center justify-between text-[11px]">
                                    <span class="text-zinc-500">Terakhir Terpakai:</span>
                                    <span class="font-medium text-amber-300">
                                        {{ $part->last_used_date ? \Carbon\Carbon::parse($part->last_used_date)->translatedFormat('d M Y') : '-' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-3 pt-3 border-t border-zinc-800/60 text-[11px] text-zinc-500 flex items-center justify-between">
                <span>Rekomendasi:</span>
                <span class="text-amber-400 font-medium">Batasi Pembelian Baru</span>
            </div>
        </div>

        {{-- CARD 3: DEAD STOCK --}}
        <div class="bg-[#181A1A] border border-rose-900/40 rounded-2xl p-5 shadow-lg relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-red-600"></div>

            <div>
                {{-- Header with Individual Period Filter --}}
                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-zinc-800/80">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-950/60 border border-rose-800/50 flex items-center justify-center text-rose-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-100">Dead Stock</h3>
                            <p class="text-[11px] text-zinc-500">0 Transaksi</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center">
                        <input type="hidden" name="period_fast" value="{{ $periodFast }}">
                        <input type="hidden" name="period_slow" value="{{ $periodSlow }}">
                        <select name="period_dead" onchange="this.form.submit()"
                                class="bg-[#121414] border border-[#2E3030] text-rose-400 text-[11px] font-semibold rounded-lg px-6 py-1 focus:outline-none focus:border-rose-500 cursor-pointer">
                            <option value="7" {{ $periodDead === '7' ? 'selected' : '' }}>7 Hari</option>
                            <option value="30" {{ $periodDead === '30' ? 'selected' : '' }}>30 Hari</option>
                            <option value="365" {{ $periodDead === '365' ? 'selected' : '' }}>1 Tahun</option>
                        </select>
                    </form>
                </div>

                @if($deadStockParts->isEmpty())
                    <div class="py-8 text-center border border-dashed border-zinc-800 rounded-xl my-2">
                        <svg class="w-8 h-8 text-zinc-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-zinc-500 font-medium">Bebas Dead Stock!</p>
                        <p class="text-[11px] text-zinc-650 mt-0.5">Semua item katalog pernah digunakan dalam periode ini.</p>
                    </div>
                @else
                    <div class="space-y-3 my-2">
                        @foreach($deadStockParts as $part)
                            <div @click="activePart = {
                                    name: '{{ addslashes($part->name) }}',
                                    category: '{{ addslashes($part->category ?? 'Katalog Bengkel') }}',
                                    type: 'dead',
                                    typeName: 'Dead Stock',
                                    qty: '0 unit',
                                    remainingStock: '{{ $part->stock }} unit',
                                    revenue: 'Rp 0',
                                    price: '{{ $part->formatted_price }}',
                                    lastUsed: '0 Transaksi ({{ $periodDead }} Hari Terakhir)',
                                    recommendation: 'Barang tidak tersentuh transaksi dalam periode ini. Disarankan membuat promo diskon atau bundling dengan jasa servis.'
                                 }; showModal = true"
                                 class="p-3 bg-zinc-900/60 border border-zinc-800/80 rounded-xl hover:border-rose-700/60 hover:bg-zinc-850/80 hover:scale-[1.01] transition-all cursor-pointer"
                                 title="Klik untuk melihat detail produk">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-zinc-200 truncate">{{ $part->name }}</p>
                                        <p class="text-[11px] text-zinc-500 mt-0.5">{{ $part->category ?? 'Katalog Bengkel' }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-lg bg-rose-950/70 border border-rose-900/50 text-rose-400 text-xs font-bold flex-shrink-0">
                                        0 unit
                                    </span>
                                </div>
                                <div class="mt-2 pt-2 border-t border-zinc-850/80 flex items-center justify-between text-[11px]">
                                    <span class="text-zinc-500">Harga Katalog:</span>
                                    <span class="font-semibold text-rose-300">{{ $part->formatted_price }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-3 pt-3 border-t border-zinc-800/60 text-[11px] text-zinc-500 flex items-center justify-between">
                <span>Rekomendasi:</span>
                <span class="text-rose-400 font-medium">Buat Promo / Bundling Diskon</span>
            </div>
        </div>

    </div>

    {{-- SPAREPART DETAIL MODAL POP-UP --}}
    <div x-cloak
         x-show="showModal"
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
         style="display: none;">

        {{-- Backdrop Overlay --}}
        <div x-show="showModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showModal = false"
             class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

        {{-- Modal Content Card --}}
        <div x-show="showModal"
             x-transition:enter="ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg bg-[#181A1A] border border-[#2E3030] rounded-2xl shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">

            {{-- Modal Top Accent Bar --}}
            <div class="h-1.5 w-full"
                 :class="{
                    'bg-gradient-to-r from-emerald-500 to-teal-400': activePart?.type === 'fast',
                    'bg-gradient-to-r from-amber-500 to-orange-400': activePart?.type === 'slow',
                    'bg-gradient-to-r from-rose-500 to-red-600': activePart?.type === 'dead'
                 }"></div>

            {{-- Modal Header --}}
            <div class="p-5 border-b border-zinc-800/80 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border"
                              :class="{
                                'bg-emerald-950/80 text-emerald-400 border-emerald-800/80': activePart?.type === 'fast',
                                'bg-amber-950/80 text-amber-400 border-amber-800/80': activePart?.type === 'slow',
                                'bg-rose-950/80 text-rose-400 border-rose-800/80': activePart?.type === 'dead'
                              }"
                              x-text="activePart?.typeName"></span>
                        <span class="text-xs text-zinc-500 font-medium" x-text="activePart?.category"></span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-100 leading-tight" x-text="activePart?.name"></h3>
                </div>

                <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-200 bg-zinc-850 hover:bg-zinc-800 p-2 rounded-xl border border-zinc-750 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-4 overflow-y-auto">

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 bg-zinc-900/90 border border-emerald-900/50 rounded-xl col-span-2 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider">Stok Tersisa Saat Ini</p>
                            <p class="text-lg font-black text-emerald-400 mt-0.5" x-text="activePart?.remainingStock"></p>
                        </div>
                        <span class="px-3 py-1 rounded-lg bg-emerald-950/80 border border-emerald-800/80 text-emerald-400 text-xs font-bold">
                            Katalog Bengkel
                        </span>
                    </div>

                    <div class="p-3.5 bg-zinc-900/80 border border-zinc-800 rounded-xl">
                        <p class="text-[11px] text-zinc-500 font-medium">Total Terpakai</p>
                        <p class="text-sm font-extrabold text-zinc-100 mt-0.5" x-text="activePart?.qty"></p>
                    </div>

                    <div class="p-3.5 bg-zinc-900/80 border border-zinc-800 rounded-xl">
                        <p class="text-[11px] text-zinc-500 font-medium" x-text="activePart?.type === 'dead' ? 'Harga Katalog' : 'Estimasi Omset'"></p>
                        <p class="text-sm font-extrabold mt-0.5"
                           :class="activePart?.type === 'dead' ? 'text-rose-300' : 'text-emerald-300'"
                           x-text="activePart?.type === 'dead' ? activePart?.price : activePart?.revenue"></p>
                    </div>

                    <div class="p-3.5 bg-zinc-900/80 border border-zinc-800 rounded-xl">
                        <p class="text-[11px] text-zinc-500 font-medium">Status Terakhir</p>
                        <p class="text-xs font-semibold text-zinc-300 mt-0.5" x-text="activePart?.lastUsed"></p>
                    </div>

                    <div class="p-3.5 bg-zinc-900/80 border border-zinc-800 rounded-xl">
                        <p class="text-[11px] text-zinc-500 font-medium">Harga / Satuan</p>
                        <p class="text-xs font-semibold text-zinc-300 mt-0.5" x-text="activePart?.price"></p>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 bg-zinc-900/60 border-t border-zinc-800/80 flex items-center justify-between gap-3">
                <a href="{{ route('workshop.spareparts.index') }}" class="btn btn-secondary text-xs flex items-center gap-1.5 py-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                    Kelola di Katalog
                </a>
                <button @click="showModal = false" class="btn btn-primary text-xs px-4 py-2">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ============================================================
     Chart.js Integration
     ============================================================ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('servicesChart').getContext('2d');

        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartValues) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kendaraan Dilayani',
                    data: data,
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#121414',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1E2020',
                        titleColor: '#F4F4F5',
                        bodyColor: '#A1A1AA',
                        borderColor: '#2E3030',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' kendaraan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#71717A',
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#2E3030',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#71717A',
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            stepSize: 1,
                            precision: 0
                        },
                        min: 0
                    }
                }
            }
        });
    });
</script>
@endpush
