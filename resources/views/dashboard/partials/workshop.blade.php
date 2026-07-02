{{-- ============================================================
     Workshop Admin Dashboard Partial
     Variables: $workshop, $totalServices, $thisMonthServices,
                $recentServices, $activeCustomers
     ============================================================ --}}

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Bengkel</h1>
        <p class="page-subtitle">{{ $workshop->name }}</p>
    </div>
</div>

{{-- ============================================================
     KPI Stats Grid — 3 cols desktop / 1 col → 3 cols mobile
     ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Total Service --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(59,130,246,0.1);color:#60a5fa;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $totalServices }}</div>
        <div class="stat-card-label">Total Service</div>
    </div>

    {{-- Service Bulan Ini --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(34,197,94,0.1);color:#4ade80;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $thisMonthServices }}</div>
        <div class="stat-card-label">Service Bulan Ini</div>
    </div>

    {{-- Active Customers --}}
    <div class="stat-card">
        <div class="stat-card-icon" style="background-color:rgba(245,158,11,0.1);color:#fbbf24;">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="stat-card-value">{{ $activeCustomers }}</div>
        <div class="stat-card-label">Active Customers</div>
    </div>

</div>

{{-- ============================================================
     Recent Services Card
     ============================================================ --}}
<div class="card">
    <div class="section-title">
        <h3>Service Terbaru</h3>
    </div>

    @if($recentServices->isEmpty())
        {{-- Empty State --}}
        <div class="empty-state" style="padding:40px 0;">
            <div class="empty-state-icon">
                <svg style="width:28px;height:28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="empty-state-title">Belum ada service</p>
        </div>
    @else
        {{-- Service List --}}
        <div style="display:flex;flex-direction:column;gap:0;">
            @foreach($recentServices as $service)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 0;{{ !$loop->last ? 'border-bottom:1px solid #2A2D2D;' : '' }}">

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
                    <div style="flex-shrink:0;text-align:right;min-width:80px;">
                        @if($service->cost && $service->cost > 0)
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
