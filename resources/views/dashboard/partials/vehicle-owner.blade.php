{{-- ============================================================
     Vehicle Owner Dashboard Partial
     Variables: $totalVehicles, $avgHealthScore, $healthStatus,
                $upcomingService, $recentActivities, $recentVehicles,
                $totalServices
     ============================================================ --}}

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang, {{ Auth::user()->name }}!</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="btn-secondary btn-sm">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export
        </button>
        <a href="#" class="btn-primary btn-sm">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kendaraan
        </a>
    </div>
</div>

{{-- ============================================================
     KPI Stats Grid — 4 cols desktop / 2 cols mobile
     ============================================================ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Kendaraan --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(65,0,8,0.2);color:#ff9aa4;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalVehicles }}</div>
        <div class="stat-card-label">Total Kendaraan</div>
        @if($totalVehicles === 0)
            <span class="stat-card-trend"
                  style="background-color:#2A2D2D;color:#71717A;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;padding:2px 6px;border-radius:4px;margin-top:8px;">
                Belum ada data
            </span>
        @endif
    </div>

    {{-- Health Score --}}
    @php
        $healthColor = match($healthStatus) {
            'good'     => '#4ade80',
            'warning'  => '#fbbf24',
            'critical' => '#f87171',
            default    => '#71717A',
        };
        $healthBg = match($healthStatus) {
            'good'     => 'rgba(74,222,128,0.1)',
            'warning'  => 'rgba(251,191,36,0.1)',
            'critical' => 'rgba(248,113,113,0.1)',
            default    => 'rgba(113,113,122,0.1)',
        };
    @endphp
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:{{ $healthBg }};color:{{ $healthColor }};">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <div class="stat-card-value" style="color:{{ $healthColor }};">
            {{ $avgHealthScore ?? '—' }}
        </div>
        <div class="stat-card-label">Health Score</div>
    </div>

    {{-- Service Berikutnya --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(245,158,11,0.1);color:#fbbf24;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-card-value">
            @if($upcomingService && $upcomingService->next_service_date)
                {{ $upcomingService->next_service_date->format('d M Y') }}
            @else
                —
            @endif
        </div>
        <div class="stat-card-label">Service Berikutnya</div>
    </div>

    {{-- Total Service --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(59,130,246,0.1);color:#60a5fa;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalServices }}</div>
        <div class="stat-card-label">Total Service</div>
    </div>

</div>

{{-- ============================================================
     Main Content Grid — 2/3 left + 1/3 right
     ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ========================
         LEFT — Kendaraan Saya
         ======================== --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="section-title">
                <h3>Kendaraan Saya</h3>
                <a href="#">Lihat Semua →</a>
            </div>

            @if($recentVehicles->isEmpty())
                {{-- Empty State --}}
                <div class="empty-state" style="padding:40px 0;">
                    <div class="empty-state-icon">
                        <svg style="width:28px;height:28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title">Belum ada kendaraan</p>
                    <p class="empty-state-desc">Tambahkan kendaraan pertama Anda untuk mulai mencatat histori service</p>
                    <div style="margin-top:20px;">
                        <a href="#" class="btn-primary btn-sm">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Kendaraan
                        </a>
                    </div>
                </div>
            @else
                {{-- Vehicle List --}}
                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach($recentVehicles as $vehicle)
                        @php
                            $vHealthColor = match($vehicle->health_status) {
                                'good'     => '#4ade80',
                                'warning'  => '#fbbf24',
                                'critical' => '#f87171',
                                default    => '#71717A',
                            };
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid #2A2D2D;' : '' }}">
                            <div style="display:flex;align-items:center;gap:12px;">
                                {{-- Health Dot --}}
                                <div style="width:10px;height:10px;border-radius:50%;background-color:{{ $vHealthColor }};flex-shrink:0;"></div>
                                <div>
                                    <p style="font-size:14px;font-weight:600;color:#F4F4F5;margin:0;">
                                        {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})
                                    </p>
                                    <p style="font-size:12px;color:#71717A;margin:2px 0 0;">
                                        {{ $vehicle->plate_number }}
                                    </p>
                                </div>
                            </div>
                            <a href="#"
                               style="font-size:12px;font-weight:500;color:#A1A1AA;white-space:nowrap;text-decoration:none;"
                               onmouseover="this.style.color='#F4F4F5'"
                               onmouseout="this.style.color='#A1A1AA'">
                                Lihat →
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ========================
         RIGHT — Quick Access + Aktivitas Terbaru
         ======================== --}}
    <div class="flex flex-col gap-4">

        {{-- Quick Access --}}
        <div class="card">
            <div class="section-title">
                <h3>Quick Access</h3>
            </div>
            <div class="grid grid-cols-2 gap-3">

                {{-- Tambah Kendaraan --}}
                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg text-center"
                   style="background-color:rgba(65,0,8,0.15);border:1px solid rgba(255,255,255,0.05);text-decoration:none;transition:filter 0.15s;"
                   onmouseover="this.style.filter='brightness(1.15)'"
                   onmouseout="this.style.filter=''">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:#ff9aa4;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span style="font-size:11px;font-weight:500;color:#A1A1AA;line-height:1.3;">Tambah Kendaraan</span>
                </a>

                {{-- Lihat QR --}}
                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg text-center"
                   style="background-color:rgba(34,197,94,0.1);border:1px solid rgba(255,255,255,0.05);text-decoration:none;transition:filter 0.15s;"
                   onmouseover="this.style.filter='brightness(1.15)'"
                   onmouseout="this.style.filter=''">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:#4ade80;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z"/>
                            <rect x="16" y="16" width="2" height="2" fill="currentColor"/>
                            <rect x="20" y="20" width="2" height="2" fill="currentColor"/>
                            <rect x="16" y="20" width="2" height="2" fill="currentColor"/>
                            <rect x="20" y="16" width="2" height="2" fill="currentColor"/>
                        </svg>
                    </div>
                    <span style="font-size:11px;font-weight:500;color:#A1A1AA;line-height:1.3;">Lihat QR</span>
                </a>

                {{-- Riwayat Service --}}
                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg text-center"
                   style="background-color:rgba(59,130,246,0.1);border:1px solid rgba(255,255,255,0.05);text-decoration:none;transition:filter 0.15s;"
                   onmouseover="this.style.filter='brightness(1.15)'"
                   onmouseout="this.style.filter=''">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:#60a5fa;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span style="font-size:11px;font-weight:500;color:#A1A1AA;line-height:1.3;">Riwayat Service</span>
                </a>

                {{-- Cari Bengkel --}}
                <a href="#"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg text-center"
                   style="background-color:rgba(245,158,11,0.1);border:1px solid rgba(255,255,255,0.05);text-decoration:none;transition:filter 0.15s;"
                   onmouseover="this.style.filter='brightness(1.15)'"
                   onmouseout="this.style.filter=''">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:#fbbf24;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span style="font-size:11px;font-weight:500;color:#A1A1AA;line-height:1.3;">Cari Bengkel</span>
                </a>

            </div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="card">
            <div class="section-title">
                <h3>Aktivitas Terbaru</h3>
            </div>

            @if($recentActivities->isEmpty())
                <div class="empty-state" style="padding:24px 0;">
                    <div class="empty-state-icon" style="width:48px;height:48px;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title" style="font-size:13px;">Belum ada aktivitas</p>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:0;">
                    @foreach($recentActivities as $activity)
                        <div style="padding:10px 0;{{ !$loop->last ? 'border-bottom:1px solid #2A2D2D;' : '' }}">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                                <div style="flex:1;min-width:0;">
                                    <p style="font-size:13px;font-weight:500;color:#F4F4F5;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        {{ $activity->vehicle?->brand }} {{ $activity->vehicle?->model }}
                                    </p>
                                    <p style="font-size:11px;color:#A1A1AA;margin:2px 0 0;">
                                        {{ $activity->serviceTypeLabelReadable }}
                                        @if($activity->workshop)
                                            · {{ $activity->workshop->name }}
                                        @endif
                                    </p>
                                </div>
                                <span style="font-size:11px;color:#71717A;white-space:nowrap;flex-shrink:0;">
                                    {{ $activity->service_date?->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
