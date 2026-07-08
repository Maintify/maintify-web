{{-- Vehicle Owner Dashboard --}}

{{-- ── Hero Greeting Banner ── --}}
<div style="background:linear-gradient(135deg,#1a0204 0%,#2a0408 50%,#1a0204 100%);border:1px solid rgba(65,0,8,0.4);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(65,0,8,0.3) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:100px;background:rgba(255,154,164,0.1);border:1px solid rgba(255,154,164,0.2);margin-bottom:10px;">
                <span style="width:5px;height:5px;border-radius:50%;background:#ff9aa4;display:inline-block;"></span>
                <span style="font-size:11px;font-weight:600;color:#ff9aa4;letter-spacing:0.04em;">DASHBOARD KENDARAAN</span>
            </div>
            <h1 style="font-size:22px;font-weight:800;color:#F4F4F5;margin:0 0 4px;letter-spacing:-0.02em;">
                Halo, {{ Auth::user()->name }} 👋
            </h1>
            <p style="font-size:13px;color:#A1A1AA;margin:0;">Pantau kondisi kendaraan Anda hari ini</p>
        </div>
        <a href="{{ route('vehicles.create') }}" class="btn-primary btn-sm" style="white-space:nowrap;">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kendaraan
        </a>
    </div>
</div>

{{-- ── Pending Transfers ── --}}
@if(isset($pendingTransfers) && $pendingTransfers->count() > 0)
    <div style="margin-bottom: 24px;">
        @foreach($pendingTransfers as $transfer)
            <div style="background: linear-gradient(145deg, rgba(59, 130, 246, 0.08) 0%, rgba(59, 130, 246, 0.02) 100%); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 16px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #60a5fa; flex-shrink: 0;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #F4F4F5; margin: 0 0 2px;">Permintaan Transfer Kendaraan</h4>
                        <p style="font-size: 13px; color: #A1A1AA; margin: 0;">
                            <strong>{{ $transfer->fromUser->name }}</strong> ingin mentransfer kendaraan <strong>{{ $transfer->vehicle->brand }} {{ $transfer->vehicle->model }}</strong> ({{ $transfer->vehicle->plate_number }}) kepada Anda.
                        </p>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <form action="{{ route('transfers.reject', $transfer) }}" method="POST">
                        @csrf
                        <button type="submit" style="padding: 8px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; color: #f87171; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 150ms;" onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'">
                            Tolak
                        </button>
                    </form>
                    <form action="{{ route('transfers.approve', $transfer) }}" method="POST">
                        @csrf
                        <button type="submit" style="padding: 8px 16px; background: #60a5fa; border: 1px solid #60a5fa; border-radius: 8px; color: #121414; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 150ms;" onmouseover="this.style.background='#93c5fd'" onmouseout="this.style.background='#60a5fa'">
                            Terima Transfer
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- ── KPI Stats ── --}}
@php
    $healthColor = match($healthStatus) { 'good' => '#4ade80', 'warning' => '#fbbf24', 'critical' => '#f87171', default => '#71717A' };
    $healthBg    = match($healthStatus) { 'good' => 'rgba(74,222,128,0.1)', 'warning' => 'rgba(251,191,36,0.1)', 'critical' => 'rgba(248,113,113,0.1)', default => 'rgba(113,113,122,0.08)' };
    $healthLabel = match($healthStatus) { 'good' => 'Baik', 'warning' => 'Perlu Perhatian', 'critical' => 'Kritis', default => 'Belum ada data' };
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Kendaraan --}}
    <div class="stat-card" style="position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:rgba(65,0,8,0.15);pointer-events:none;"></div>
        <div class="stat-card-icon" style="background:rgba(65,0,8,0.2);color:#ff9aa4;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalVehicles }}</div>
        <div class="stat-card-label">Total Kendaraan</div>
        @if($totalVehicles === 0)
            <span style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;font-weight:500;padding:2px 8px;border-radius:6px;background:#2A2D2D;color:#71717A;">Tambahkan kendaraan</span>
        @else
            <span style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;font-weight:500;padding:2px 8px;border-radius:6px;background:rgba(65,0,8,0.15);color:#ff9aa4;">● Aktif</span>
        @endif
    </div>

    {{-- Health Score --}}
    <div class="stat-card" style="position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:{{ $healthBg }};pointer-events:none;"></div>
        <div class="stat-card-icon" style="background:{{ $healthBg }};color:{{ $healthColor }};">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <div class="stat-card-value" style="color:{{ $healthColor }};">{{ $avgHealthScore ? round($avgHealthScore) : '—' }}</div>
        <div class="stat-card-label">Health Score</div>
        <span style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;font-weight:500;padding:2px 8px;border-radius:6px;background:{{ $healthBg }};color:{{ $healthColor }};">{{ $healthLabel }}</span>
    </div>

    {{-- Upcoming Service --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background:rgba(245,158,11,0.1);color:#fbbf24;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-card-value" style="font-size:16px;">
            @if($upcomingService?->next_service_date)
                {{ $upcomingService->next_service_date->format('d M') }}
                <span style="font-size:13px;color:#71717A;font-weight:400;">{{ $upcomingService->next_service_date->format('Y') }}</span>
            @else —
            @endif
        </div>
        <div class="stat-card-label">Service Berikutnya</div>
        @if($upcomingService?->next_service_date)
            @php $daysLeft = now()->diffInDays($upcomingService->next_service_date, false); @endphp
            <span style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;font-weight:500;padding:2px 8px;border-radius:6px;background:{{ $daysLeft < 7 ? 'rgba(248,113,113,0.1)' : 'rgba(245,158,11,0.1)' }};color:{{ $daysLeft < 7 ? '#f87171' : '#fbbf24' }};">
                {{ $daysLeft >= 0 ? $daysLeft . ' hari lagi' : abs($daysLeft) . ' hari lewat' }}
            </span>
        @endif
    </div>

    {{-- Total Service --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background:rgba(59,130,246,0.1);color:#60a5fa;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalServices }}</div>
        <div class="stat-card-label">Total Service</div>
        <span style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;font-weight:500;padding:2px 8px;border-radius:6px;background:rgba(59,130,246,0.1);color:#60a5fa;">Riwayat tersimpan</span>
    </div>

</div>

{{-- ── Main Grid ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- LEFT: Kendaraan Saya --}}
    <div class="lg:col-span-2 flex flex-col gap-4">

        <div class="card">
            <div class="section-title">
                <div>
                    <h3>Kendaraan Saya</h3>
                    @if(!$recentVehicles->isEmpty())
                        <p style="font-size:11px;color:#71717A;margin:2px 0 0;">{{ $totalVehicles }} kendaraan terdaftar</p>
                    @endif
                </div>
                <a href="{{ route('vehicles.index') }}" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:500;color:#ff9aa4;text-decoration:none;">
                    Lihat Semua
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($recentVehicles->isEmpty())
                <div class="empty-state" style="padding:36px 0;">
                    <div class="empty-state-icon">
                        <svg style="width:26px;height:26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title">Belum ada kendaraan</p>
                    <p class="empty-state-desc">Tambahkan kendaraan untuk mulai memantau histori service</p>
                    <div style="margin-top:16px;">
                        <a href="{{ route('vehicles.create') }}" class="btn-primary btn-sm">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Kendaraan Pertama
                        </a>
                    </div>
                </div>
            @else
                <div style="display:flex;flex-direction:column;">
                    @foreach($recentVehicles as $vehicle)
                        @php
                            $vc = match($vehicle->health_status) { 'good' => '#4ade80', 'warning' => '#fbbf24', 'critical' => '#f87171', default => '#71717A' };
                            $vbg = match($vehicle->health_status) { 'good' => 'rgba(74,222,128,0.08)', 'warning' => 'rgba(251,191,36,0.08)', 'critical' => 'rgba(248,113,113,0.08)', default => 'rgba(113,113,122,0.06)' };
                            $vlabel = match($vehicle->health_status) { 'good' => 'Baik', 'warning' => 'Perlu Service', 'critical' => 'Kritis', default => 'Tidak Diketahui' };
                        @endphp
                        <a href="{{ route('vehicles.show', $vehicle) }}" style="display:flex;align-items:center;gap:14px;padding:14px 0;text-decoration:none;{{ !$loop->last ? 'border-bottom:1px solid #252828;' : '' }};transition:opacity 150ms;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            {{-- Vehicle Icon --}}
                            <div style="width:46px;height:46px;border-radius:14px;background:{{ $vbg }};border:1px solid {{ $vc }}33;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg style="width:22px;height:22px;color:{{ $vc }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                                </svg>
                            </div>
                            {{-- Info --}}
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:14px;font-weight:600;color:#F4F4F5;margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                    <span style="font-size:12px;font-weight:400;color:#71717A;">({{ $vehicle->year }})</span>
                                </p>
                                <p style="font-size:12px;color:#71717A;margin:0;display:flex;align-items:center;gap:8px;">
                                    <span>{{ $vehicle->plate_number }}</span>
                                    @if($vehicle->current_odometer)
                                        <span style="color:#3A3D3D;">·</span>
                                        <span>{{ number_format($vehicle->current_odometer) }} km</span>
                                    @endif
                                </p>
                            </div>
                            {{-- Health Badge --}}
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:600;background:{{ $vbg }};color:{{ $vc }};border:1px solid {{ $vc }}33;white-space:nowrap;flex-shrink:0;">
                                <span style="width:5px;height:5px;border-radius:50%;background:{{ $vc }};"></span>
                                {{ $vlabel }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Activity --}}
        <div class="card">
            <div class="section-title">
                <h3>Aktivitas Service Terbaru</h3>
                <span style="font-size:11px;color:#71717A;">{{ $recentActivities->count() }} entri</span>
            </div>
            @if($recentActivities->isEmpty())
                <div class="empty-state" style="padding:24px 0;">
                    <div class="empty-state-icon" style="width:44px;height:44px;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title" style="font-size:13px;">Belum ada riwayat service</p>
                </div>
            @else
                <div>
                    @foreach($recentActivities as $activity)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid #252828;' : '' }}">
                            <div style="width:36px;height:36px;border-radius:10px;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg style="width:16px;height:16px;color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:600;color:#F4F4F5;margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $activity->vehicle?->brand }} {{ $activity->vehicle?->model }}
                                </p>
                                <p style="font-size:11px;color:#71717A;margin:0;">
                                    {{ $activity->serviceTypeLabelReadable }}
                                    @if($activity->workshop) · {{ $activity->workshop->name }} @endif
                                </p>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <p style="font-size:11px;color:#71717A;margin:0;">{{ $activity->service_date?->format('d M Y') }}</p>
                                @if($activity->total_cost > 0)
                                    <p style="font-size:12px;font-weight:600;color:#4ade80;margin:2px 0 0;">{{ $activity->formattedCost }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Quick Access --}}
    <div class="flex flex-col gap-4">

        {{-- Quick Actions --}}
        <div class="card">
            <div class="section-title"><h3>Akses Cepat</h3></div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                @php
                    $quickActions = [
                        ['icon' => 'M12 4v16m8-8H4', 'label' => 'Tambah Kendaraan', 'desc' => 'Daftarkan kendaraan baru', 'color' => '#ff9aa4', 'bg' => 'rgba(65,0,8,0.15)', 'border' => 'rgba(65,0,8,0.3)', 'route' => route('vehicles.create')],
                        ['icon' => 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z', 'label' => 'Lihat QR Code', 'desc' => 'Identitas digital kendaraan', 'color' => '#4ade80', 'bg' => 'rgba(34,197,94,0.08)', 'border' => 'rgba(34,197,94,0.2)', 'route' => route('vehicles.index')],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Riwayat Service', 'desc' => 'Lihat semua histori', 'color' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.08)', 'border' => 'rgba(59,130,246,0.2)', 'route' => route('vehicles.index')],
                        ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Cari Bengkel', 'desc' => 'Bengkel terdekat dari Anda', 'color' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.08)', 'border' => 'rgba(245,158,11,0.2)', 'route' => '#'],
                    ];
                @endphp
                @foreach($quickActions as $action)
                    <a href="{{ $action['route'] }}" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:{{ $action['bg'] }};border:1px solid {{ $action['border'] }};text-decoration:none;transition:filter 150ms;" onmouseover="this.style.filter='brightness(1.12)'" onmouseout="this.style.filter=''">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:{{ $action['color'] }};">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $action['icon'] }}"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:600;color:#F4F4F5;margin:0 0 1px;">{{ $action['label'] }}</p>
                            <p style="font-size:11px;color:#71717A;margin:0;">{{ $action['desc'] }}</p>
                        </div>
                        <svg style="width:14px;height:14px;color:#3A3D3D;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Upcoming Service Alert --}}
        @if($upcomingService?->next_service_date)
        @php $dLeft = now()->diffInDays($upcomingService->next_service_date, false); @endphp
        <div style="background:{{ $dLeft < 7 ? 'rgba(248,113,113,0.06)' : 'rgba(245,158,11,0.06)' }};border:1px solid {{ $dLeft < 7 ? 'rgba(248,113,113,0.25)' : 'rgba(245,158,11,0.25)' }};border-radius:16px;padding:16px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <div style="width:32px;height:32px;border-radius:10px;background:{{ $dLeft < 7 ? 'rgba(248,113,113,0.12)' : 'rgba(245,158,11,0.12)' }};display:flex;align-items:center;justify-content:center;">
                    <svg style="width:16px;height:16px;color:{{ $dLeft < 7 ? '#f87171' : '#fbbf24' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:12px;font-weight:700;color:{{ $dLeft < 7 ? '#f87171' : '#fbbf24' }};margin:0;">{{ $dLeft < 0 ? '⚠️ Service Terlambat!' : '🔔 Pengingat Service' }}</p>
                    <p style="font-size:11px;color:#71717A;margin:0;">{{ $upcomingService->brand }} {{ $upcomingService->model }}</p>
                </div>
            </div>
            <p style="font-size:12px;color:#A1A1AA;margin:0 0 10px;line-height:1.5;">
                @if($dLeft < 0)
                    Sudah <strong style="color:#f87171;">{{ abs($dLeft) }} hari</strong> melewati jadwal service
                @else
                    Service berikutnya <strong style="color:{{ $dLeft < 7 ? '#f87171' : '#fbbf24' }};">{{ $dLeft }} hari lagi</strong> pada {{ $upcomingService->next_service_date->format('d M Y') }}
                @endif
            </p>
            <a href="#" class="btn-sm" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:{{ $dLeft < 7 ? 'rgba(248,113,113,0.15)' : 'rgba(245,158,11,0.15)' }};color:{{ $dLeft < 7 ? '#f87171' : '#fbbf24' }};font-size:11px;font-weight:600;text-decoration:none;border:1px solid {{ $dLeft < 7 ? 'rgba(248,113,113,0.3)' : 'rgba(245,158,11,0.3)' }};">
                Cari Bengkel →
            </a>
        </div>
        @endif

    </div>
</div>
