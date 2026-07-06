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

    {{-- Left Section (Charts & Service List) - Takes 2 cols --}}
    <div class="lg:col-span-2 flex flex-col gap-6">

        {{-- Trending Chart Card --}}
        <div class="card">
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

        {{-- Recent Services Card --}}
        <div class="card">
            <div class="section-title">
                <h3>Sesi Service Terbaru</h3>
            </div>

            @if($recentServices->isEmpty())
                <div class="empty-state" style="padding:40px 0;">
                    <div class="empty-state-icon">
                        <svg style="width:28px;height:28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="empty-state-title">Belum ada aktivitas service</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach($recentServices as $service)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--color-border);' : '' }}">

                            {{-- Left: vehicle info + owner --}}
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:14px;font-weight:600;color:#F4F4F5;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $service->vehicle->brand }} {{ $service->vehicle->model }} ({{ $service->vehicle->year }})
                                </p>
                                <p style="font-size:12px;color:#71717A;margin:2px 0 0;">
                                    {{ $service->vehicle->plate_number }}
                                    @if($service->vehicle->owner)
                                        · {{ $service->vehicle->owner->name }}
                                    @endif
                                </p>
                            </div>

                            {{-- Middle: service type + date --}}
                            <div style="flex-shrink:0;text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:2px;">
                                <span style="font-size:12px;font-weight:500;color:#A1A1AA;">
                                    {{ $service->serviceTypeLabelReadable }}
                                </span>
                                <span style="font-size:11px;color:#71717A;">
                                    {{ $service->service_date->format('d M Y') }}
                                </span>
                            </div>

                            {{-- Right: cost --}}
                            <div style="flex-shrink:0;text-align:right;min-width:100px;">
                                @if($service->cost && $service->cost > 0)
                                    <span style="font-size:13px;font-weight:600;color:#F4F4F5;">
                                        {{ $service->formatted_cost }}
                                    </span>
                                @elseif($service->total_cost && $service->total_cost > 0)
                                    <span style="font-size:13px;font-weight:600;color:#F4F4F5;">
                                        {{ $service->formatted_cost }}
                                    </span>
                                @else
                                    <span style="font-size:13px;color:#71717A;">—</span>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Right Section (Quick Actions & Top Parts) - Takes 1 col --}}
    <div class="flex flex-col gap-6">

        {{-- Quick Access Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Akses Cepat</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="#" class="btn btn-primary" style="justify-content: flex-start; text-align: left;">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8H3a2 2 0 00-2 2v6a2 2 0 002 2h2m2-12V4a2 2 0 012-2h4a2 2 0 012 2v1"/>
                    </svg>
                    Scan QR Kendaraan
                </a>
                <a href="{{ route('workshop.reports.index') }}" class="btn btn-secondary" style="justify-content: flex-start; text-align: left;">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Laporan Operasional
                </a>
                <a href="#" class="btn btn-secondary" style="justify-content: flex-start; text-align: left; opacity: 0.65; cursor: not-allowed;" onclick="event.preventDefault();">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Kelola Staf Bengkel
                </a>
                <a href="#" class="btn btn-secondary" style="justify-content: flex-start; text-align: left; opacity: 0.65; cursor: not-allowed;" onclick="event.preventDefault();">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                    Katalog Sparepart
                </a>
            </div>
        </div>

        {{-- Top Spareparts Summary --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Top Sparepart</h3>
                    <p class="card-subtitle">Suku cadang paling banyak digunakan</p>
                </div>
            </div>

            @if($topSpareparts->isEmpty())
                <div class="empty-state" style="padding:24px 0;">
                    <div class="empty-state-icon" style="width:40px;height:40px;margin-bottom:12px;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="empty-state-title" style="font-size:13px;">Belum ada sparepart digunakan</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($topSpareparts as $part)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:600;color:#F4F4F5;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $part->part_name }}
                                </p>
                            </div>
                            <span class="badge badge-primary" style="font-size:11px;font-weight:600;padding:2px 8px;">
                                {{ $part->total_quantity }} unit
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
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
