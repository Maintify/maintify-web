{{-- ============================================================
     Super Admin Dashboard Partial
     Variables: $totalUsers, $newUsersThisMonth, $totalVehicles,
                $totalWorkshops, $pendingWorkshops, $totalServiceRecords,
                $usersByRole, $workshopsByStatus, $chartLabels, $chartValues,
                $systemHealth, $pendingWorkshopsCount
     ============================================================ --}}

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">Monitoring sistem Maintify secara menyeluruh</p>
    </div>
</div>

@if(session('success'))
    <div style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

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
        <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid var(--color-border); display: flex; flex-direction: column; gap: 4px;">
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--color-text-secondary);">
                <span>Pemilik Kendaraan:</span>
                <span style="font-weight: 600; color: var(--color-text-primary);">{{ $usersByRole['vehicle_owner'] ?? 0 }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--color-text-secondary);">
                <span>Bengkel Mitra:</span>
                <span style="font-weight: 600; color: var(--color-text-primary);">{{ $usersByRole['workshop'] ?? 0 }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--color-text-secondary);">
                <span>Super Admin:</span>
                <span style="font-weight: 600; color: var(--color-text-primary);">{{ $usersByRole['super_admin'] ?? 0 }}</span>
            </div>
        </div>
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
        @if($pendingWorkshopsCount > 0)
            <span class="stat-card-trend"
                  style="background-color:#2A2D2D;color:#fbbf24;display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;padding:2px 6px;border-radius:4px;margin-top:8px;">
                {{ $pendingWorkshopsCount }} menunggu approval
            </span>
        @endif
        <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid var(--color-border); display: flex; flex-direction: column; gap: 4px;">
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--color-text-secondary);">
                <span>Terverifikasi:</span>
                <span style="font-weight: 600; color: var(--color-text-primary);">{{ $workshopsByStatus['approved'] ?? 0 }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--color-text-secondary);">
                <span>Menunggu Review:</span>
                <span style="font-weight: 600; color: var(--color-text-primary);">{{ $workshopsByStatus['pending'] ?? 0 }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--color-text-secondary);">
                <span>Ditolak / Revisi:</span>
                <span style="font-weight: 600; color: var(--color-text-primary);">{{ ($workshopsByStatus['rejected'] ?? 0) + ($workshopsByStatus['revision_needed'] ?? 0) }}</span>
            </div>
        </div>
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
     Main Dashboard Layout Grid
     ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left Section (Growth Chart) - Takes 2 cols --}}
    <div class="lg:col-span-2 flex flex-col gap-6">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Pertumbuhan Platform</h3>
                    <p class="card-subtitle">Statistik pendaftaran pengguna baru 7 hari terakhir</p>
                </div>
                <span class="badge badge-primary">Real-Time</span>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Right Section (System Health) - Takes 1 col --}}
    <div class="flex flex-col gap-6">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Kesehatan Sistem</h3>
                    <p class="card-subtitle">Status operasional server dan database</p>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 8px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: block; width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e; box-shadow: 0 0 8px #22c55e;"></span>
                        <span style="font-size: 13px; font-weight: 500; color: var(--color-text-primary);">Koneksi Database</span>
                    </div>
                    <span class="badge badge-success" style="font-size: 11px; font-weight: 600; padding: 2px 8px;">{{ $systemHealth['db_status'] }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: block; width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e; box-shadow: 0 0 8px #22c55e;"></span>
                        <span style="font-size: 13px; font-weight: 500; color: var(--color-text-primary);">Uptime Server</span>
                    </div>
                    <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ $systemHealth['uptime'] }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: block; width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e; box-shadow: 0 0 8px #22c55e;"></span>
                        <span style="font-size: 13px; font-weight: 500; color: var(--color-text-primary);">Tingkat Error</span>
                    </div>
                    <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ $systemHealth['error_rate'] }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: block; width: 8px; height: 8px; border-radius: 50%; background-color: #22c55e; box-shadow: 0 0 8px #22c55e;"></span>
                        <span style="font-size: 13px; font-weight: 500; color: var(--color-text-primary);">Total Request API</span>
                    </div>
                    <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ number_format($systemHealth['api_requests']) }} req</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ============================================================
     Pending Verification Queue Card
     ============================================================ --}}
<div class="card" style="margin-top: 24px;">
    <div class="card-header" style="border-bottom: 1px solid var(--color-border); padding-bottom: 16px; margin-bottom: 16px;">
        <div>
            <h3 class="card-title">Antrean Verifikasi Bengkel</h3>
            <p class="card-subtitle">Daftar bengkel mitra baru yang menunggu persetujuan bergabung</p>
        </div>
        <span class="badge badge-warning" style="font-size: 12px; font-weight: 600; padding: 2px 8px; background-color: rgba(245,158,11,0.1); color: #fbbf24;">
            {{ $pendingWorkshopsCount }} Antrean
        </span>
    </div>

    @if($pendingWorkshops->isEmpty())
        <div class="empty-state" style="padding: 40px 0; text-align: center;">
            <div class="empty-state-icon" style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background-color: rgba(34,197,94,0.1); color: #22c55e; margin-bottom: 12px;">
                <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="empty-state-title" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary); margin: 0;">Semua pendaftaran telah terproses</p>
            <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0 0;">Tidak ada bengkel yang menunggu verifikasi saat ini.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--color-border); font-size: 11px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 12px 16px; font-weight: 600;">Bengkel</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Pemilik</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Kontak & Alamat</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Dokumen</th>
                        <th style="padding: 12px 16px; font-weight: 600; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    @foreach($pendingWorkshops as $workshop)
                        <tr style="border-bottom: 1px solid var(--color-border); vertical-align: middle;">
                            <td style="padding: 16px; font-weight: 600; color: var(--color-text-primary);">
                                {{ $workshop->name }}
                            </td>
                            <td style="padding: 16px;">
                                <div style="font-weight: 500; color: var(--color-text-primary);">{{ $workshop->owner_name ?? '-' }}</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">KTP: {{ $workshop->owner_ktp_number ?? '-' }}</div>
                            </td>
                            <td style="padding: 16px;">
                                <div style="color: var(--color-text-primary);">{{ $workshop->email }} &middot; {{ $workshop->phone }}</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">{{ $workshop->address }}, {{ $workshop->city }}</div>
                            </td>
                            <td style="padding: 16px;">
                                @if($workshop->legal_document_url)
                                    <a href="{{ $workshop->legal_document_url }}" target="_blank" class="inline-flex items-center gap-1" style="color: #60a5fa; font-weight: 500; font-size: 12px;">
                                        <svg style="width:14px;height:14px; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Dokumen Legalitas
                                    </a>
                                @else
                                    <span style="color: var(--color-text-muted); font-size: 11px;">Tidak ada dokumen</span>
                                @endif
                            </td>
                            <td style="padding: 16px; text-align: right; min-width: 180px;">
                                <div style="display: inline-flex; align-items: center; gap: 8px; justify-content: flex-end;">
                                    {{-- Approve Form --}}
                                    <form action="{{ route('admin.workshops.approve', $workshop->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui bengkel ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" style="padding: 6px 12px; font-size: 11px; font-weight: 600; border-radius: 6px; background-color: #22c55e; color: #fff; cursor: pointer; border: none; transition: background var(--transition-fast);">
                                            Setujui
                                        </button>
                                    </form>

                                    {{-- Reject Button --}}
                                    <button type="button" onclick="toggleRejectForm({{ $workshop->id }})" class="btn btn-sm btn-danger" style="padding: 6px 12px; font-size: 11px; font-weight: 600; border-radius: 6px; background-color: #ef4444; color: #fff; cursor: pointer; border: none; transition: background var(--transition-fast);">
                                        Tolak
                                    </button>
                                </div>

                                {{-- Inline Rejection Form Drawer --}}
                                <div id="reject-drawer-{{ $workshop->id }}" style="display: none; margin-top: 12px; padding: 12px; background-color: rgba(239, 68, 68, 0.05); border: 1px dashed rgba(239, 68, 68, 0.2); border-radius: 8px; text-align: left;">
                                    <form action="{{ route('admin.workshops.reject', $workshop->id) }}" method="POST">
                                        @csrf
                                        <label style="display: block; font-size: 11px; font-weight: 600; color: #f87171; margin-bottom: 6px; text-transform: uppercase;">Alasan Penolakan (Wajib)</label>
                                        <textarea name="rejection_reason" required placeholder="Masukkan alasan penolakan..." style="width: 100%; min-height: 60px; background-color: #121414; border: 1px solid var(--color-border); border-radius: 6px; padding: 8px; color: #fff; font-size: 12px; outline: none; resize: vertical; margin-bottom: 8px;"></textarea>
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <button type="button" onclick="toggleRejectForm({{ $workshop->id }})" style="font-size: 11px; font-weight: 500; color: var(--color-text-secondary); background: none; border: none; padding: 4px 8px; cursor: pointer;">
                                                Batal
                                            </button>
                                            <button type="submit" style="font-size: 11px; font-weight: 600; color: #fff; background-color: #ef4444; border: none; border-radius: 4px; padding: 4px 10px; cursor: pointer;">
                                                Kirim Penolakan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ============================================================
     Chart.js & Interactivity Script Integration
     ============================================================ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleRejectForm(id) {
        const drawer = document.getElementById('reject-drawer-' + id);
        if (drawer.style.display === 'none') {
            drawer.style.display = 'block';
        } else {
            drawer.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('registrationsChart').getContext('2d');

        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
        gradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartValues) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Registrasi Baru',
                    data: data,
                    borderColor: '#ef4444',
                    borderWidth: 2,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ef4444',
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
                                return context.raw + ' pengguna';
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
