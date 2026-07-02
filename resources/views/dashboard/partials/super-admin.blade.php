{{-- ============================================================
     Super Admin Dashboard Partial
     Variables: $totalUsers, $newUsersThisMonth, $totalVehicles,
                $totalWorkshops, $pendingWorkshops, $totalServiceRecords
     ============================================================ --}}

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">Monitoring sistem Maintify</p>
    </div>
</div>

{{-- ============================================================
     KPI Stats Grid — 4 cols desktop / 2 cols mobile
     ============================================================ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Pengguna --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(239,68,68,0.1);color:#f87171;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalUsers }}</div>
        <div class="stat-card-label">Total Pengguna</div>
        <span class="stat-card-trend"
              style="background-color:#2A2D2D;color:#4ade80;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;padding:2px 6px;border-radius:4px;margin-top:8px;">
            +{{ $newUsersThisMonth }} baru bulan ini
        </span>
    </div>

    {{-- Total Kendaraan --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(245,158,11,0.1);color:#fbbf24;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalVehicles }}</div>
        <div class="stat-card-label">Total Kendaraan</div>
    </div>

    {{-- Total Bengkel --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(34,197,94,0.1);color:#4ade80;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalWorkshops }}</div>
        <div class="stat-card-label">Total Bengkel</div>
        @if($pendingWorkshops > 0)
            <span class="stat-card-trend"
                  style="background-color:#2A2D2D;color:#fbbf24;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;padding:2px 6px;border-radius:4px;margin-top:8px;">
                {{ $pendingWorkshops }} menunggu approval
            </span>
        @endif
    </div>

    {{-- Total Service Records --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(59,130,246,0.1);color:#60a5fa;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalServiceRecords }}</div>
        <div class="stat-card-label">Total Service Records</div>
    </div>

</div>

{{-- ============================================================
     Info Card — Real-time data note
     ============================================================ --}}
<div class="card" style="display:flex;align-items:center;gap:12px;">
    <div style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;color:#60a5fa;">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <p style="font-size:13px;color:#71717A;margin:0;">
        Data diperbarui secara real-time dari database
    </p>
</div>
