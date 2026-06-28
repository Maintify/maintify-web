<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="breadcrumb">Selamat datang kembali 👋</x-slot>

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Selamat datang, {{ Auth::user()->name }}!</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn-secondary btn-sm">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
            </button>
            <button class="btn-primary btn-sm">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kendaraan
            </button>
        </div>
    </div>

    {{-- KPI Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Kendaraan --}}
        <div class="stat-card">
            <div class="stat-card-icon" style="background-color:rgba(65,0,8,0.2);color:#ff9aa4;">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                </svg>
            </div>
            <div class="stat-card-value">0</div>
            <div class="stat-card-label">Total Kendaraan</div>
            <span class="stat-card-trend neutral" style="background-color:#2A2D2D;color:#71717A;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;padding:2px 6px;border-radius:4px;margin-top:8px;">
                Belum ada data
            </span>
        </div>

        {{-- Health Score --}}
        <div class="stat-card">
            <div class="stat-card-icon" style="background-color:rgba(34,197,94,0.1);color:#4ade80;">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="stat-card-value">—</div>
            <div class="stat-card-label">Health Score</div>
        </div>

        {{-- Service Berikutnya --}}
        <div class="stat-card">
            <div class="stat-card-icon" style="background-color:rgba(245,158,11,0.1);color:#fbbf24;">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-card-value">—</div>
            <div class="stat-card-label">Service Berikutnya</div>
        </div>

        {{-- Total Service --}}
        <div class="stat-card">
            <div class="stat-card-icon" style="background-color:rgba(59,130,246,0.1);color:#60a5fa;">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-card-value">0</div>
            <div class="stat-card-label">Total Service</div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Recent Vehicles --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="section-title">
                    <h3>Kendaraan Saya</h3>
                    <a href="#">Lihat Semua →</a>
                </div>

                {{-- Empty State --}}
                <div class="empty-state" style="padding:40px 0;">
                    <div class="empty-state-icon">
                        <svg style="width:28px;height:28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM8 6h4l2 4H6l2-4z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title">Belum ada kendaraan</p>
                    <p class="empty-state-desc">Tambahkan kendaraan pertama Anda untuk mulai mencatat histori service</p>
                    <div style="margin-top:20px;">
                        <button class="btn-primary btn-sm">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Kendaraan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="flex flex-col gap-4">

            {{-- Quick Actions --}}
            <div class="card">
                <div class="section-title">
                    <h3>Quick Access</h3>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['icon' => 'M12 4v16m8-8H4', 'label' => 'Tambah Kendaraan', 'color' => '#410008', 'bg' => 'rgba(65,0,8,0.15)'],
                        ['icon' => 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3z', 'label' => 'Lihat QR', 'color' => '#4ade80', 'bg' => 'rgba(34,197,94,0.1)'],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'Riwayat Service', 'color' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.1)'],
                        ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Cari Bengkel', 'color' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.1)'],
                    ] as $action)
                        <button class="flex flex-col items-center gap-2 p-3 rounded-lg transition-colors text-center"
                                style="background-color:{{ $action['bg'] }};border:1px solid rgba(255,255,255,0.05);"
                                onmouseover="this.style.filter='brightness(1.1)'"
                                onmouseout="this.style.filter=''">
                            <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;color:{{ $action['color'] }};">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $action['icon'] }}"/>
                                </svg>
                            </div>
                            <span style="font-size:11px;font-weight:500;color:#A1A1AA;line-height:1.3;">{{ $action['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card">
                <div class="section-title">
                    <h3>Aktivitas Terbaru</h3>
                </div>

                <div class="empty-state" style="padding:24px 0;">
                    <div class="empty-state-icon" style="width:48px;height:48px;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="empty-state-title" style="font-size:13px;">Belum ada aktivitas</p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
