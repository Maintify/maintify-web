@props([
    'value' => null,
    'label' => null,
    'icon'  => null,
    'trend' => null,    // up | down | null
    'color' => 'primary', // primary | success | warning | danger | info
])

@php
    $iconColors = [
        'primary' => ['bg' => 'rgba(65,0,8,0.2)', 'color' => '#ff9aa4'],
        'success' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => '#4ade80'],
        'warning' => ['bg' => 'rgba(245,158,11,0.1)', 'color' => '#fbbf24'],
        'danger'  => ['bg' => 'rgba(239,68,68,0.1)', 'color' => '#f87171'],
        'info'    => ['bg' => 'rgba(59,130,246,0.1)', 'color' => '#60a5fa'],
    ];
    $iconStyle = $iconColors[$color] ?? $iconColors['primary'];
@endphp

<div class="stat-card">
    @if($icon)
        <div class="stat-card-icon" style="background-color:{{ $iconStyle['bg'] }};color:{{ $iconStyle['color'] }};">
            {{ $icon }}
        </div>
    @endif

    <div class="stat-card-value">{{ $value }}</div>
    <div class="stat-card-label">{{ $label }}</div>

    @if($trend)
        <span class="stat-card-trend {{ $trend }}">
            @if($trend === 'up')
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            @else
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            @endif
            {{ $slot }}
        </span>
    @endif
</div>
