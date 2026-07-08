<x-app-layout>
    @slot('pageTitle', 'Detail Kendaraan')

    <div style="max-width: 1200px; margin: 0 auto; padding: 24px 16px;">
        {{-- Navigation & Header --}}
        <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <a href="{{ route('vehicles.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: #71717A; text-decoration: none; font-size: 13px; font-weight: 600; transition: color 150ms;" onmouseover="this.style.color='#F4F4F5'" onmouseout="this.style.color='#71717A'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Garasi
            </a>
            
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('vehicles.edit', $vehicle) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background-color: #252828; border: 1px solid #2E3030; border-radius: 8px; color: #F4F4F5; font-size: 13px; font-weight: 600; text-decoration: none; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='#2A2D2D';" onmouseout="this.style.backgroundColor='#252828';">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Ubah Data
                </a>
            </div>
        </div>

        {{-- Success State Notification --}}
        @if (session('success'))
            <div style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.25); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <div style="background: rgba(34,197,94,0.15); border-radius: 50%; padding: 6px; display: flex; align-items: center; justify-content: center; color: #4ade80;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 style="color: #4ade80; font-size: 14px; font-weight: 600; margin: 0 0 2px;">Berhasil!</h4>
                    <p style="color: #A1A1AA; font-size: 13px; margin: 0;">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Main Two-Column Layout Grid --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 24px; align-items: start;" class="lg:grid-cols-[380px_1fr]">
            
            {{-- ==========================================
                 LEFT COLUMN: Vehicle Identity & Overview
                 ========================================== --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- Vehicle Info Card --}}
                <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 20px; overflow: hidden;">
                    {{-- Photo Header --}}
                    <div style="position: relative; width: 100%; height: 200px; background-color: #252828; display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid #2E3030;">
                        @if($vehicle->photo_url)
                            <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <svg style="width: 80px; height: 80px; color: #71717A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M8 9h2v2H8V9zm0-4h2v2H8V5zm0 8h2v2H8v-2zM20 9h2v2h-2V9zm0-4h2v2h-2V5zm0 8h2v2h-2v-2z"/>
                            </svg>
                        @endif

                        {{-- Fuel badge overlay --}}
                        <div style="position: absolute; bottom: 12px; left: 12px; z-index: 10;">
                            @php
                                $fuelLabels = [
                                    'gasoline' => 'Bensin',
                                    'diesel' => 'Diesel',
                                    'electric' => 'Listrik',
                                    'hybrid' => 'Hybrid',
                                ];
                                $fuelLabel = $fuelLabels[$vehicle->fuel_type] ?? ucfirst($vehicle->fuel_type);
                            @endphp
                            <span style="padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em; background-color: rgba(18, 20, 20, 0.85); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); color: #F4F4F5;">
                                {{ $fuelLabel }}
                            </span>
                        </div>
                    </div>

                    {{-- Specs and Info --}}
                    <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
                        <div>
                            <h2 style="font-size: 20px; font-weight: 800; color: #F4F4F5; margin: 0 0 4px; line-height: 1.3;">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </h2>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 13px; font-weight: 700; color: #ff9aa4; font-family: monospace; letter-spacing: 0.02em;">
                                    {{ $vehicle->plate_number }}
                                </span>
                                <span style="color: #3A3D3D;">•</span>
                                <span style="font-size: 12px; font-weight: 500; color: #71717A;">
                                    Tahun {{ $vehicle->year }}
                                </span>
                            </div>
                        </div>

                        {{-- Odometer --}}
                        <div style="background-color: #252828; border: 1px solid #2E3030; border-radius: 12px; padding: 14px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 12px; font-weight: 600; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Odometer Saat Ini</span>
                            <span style="font-size: 16px; font-weight: 800; color: #F4F4F5;">{{ number_format($vehicle->current_odometer) }} Km</span>
                        </div>

                        {{-- Next Service Schedule --}}
                        <div style="background-color: #252828; border: 1px solid #2E3030; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 8px;">
                            <span style="font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Jadwal Servis Berikutnya</span>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                <span style="color: #A1A1AA;">Odometer target:</span>
                                <span style="font-weight: 700; color: #F4F4F5;">
                                    {{ $vehicle->next_service_odometer ? number_format($vehicle->next_service_odometer) . ' Km' : 'Belum Diatur' }}
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                <span style="color: #A1A1AA;">Tanggal target:</span>
                                <span style="font-weight: 700; color: #F4F4F5;">
                                    {{ $vehicle->next_service_date ? $vehicle->next_service_date->format('d M Y') : 'Belum Diatur' }}
                                </span>
                            </div>
                        </div>

                        {{-- Health Overview Bar --}}
                        <div style="display: flex; flex-direction: column; gap: 14px; padding: 16px; border: 1px solid #2E3030; border-radius: 12px;">
                            {{-- Health Score --}}
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
                                    <span style="color: #71717A; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Kesehatan Mesin</span>
                                    <span style="color: #F4F4F5; font-weight: 700;">{{ $vehicle->health_score }}%</span>
                                </div>
                                <div style="width: 100%; height: 6px; background-color: #1E2020; border-radius: 100px; overflow: hidden;">
                                    @php
                                        $healthColor = $vehicle->health_score >= 80 ? '#4ade80' : ($vehicle->health_score >= 50 ? '#fbbf24' : '#f87171');
                                    @endphp
                                    <div style="width: {{ $vehicle->health_score }}%; height: 100%; background-color: {{ $healthColor }}; border-radius: 100px;"></div>
                                </div>
                            </div>

                            <hr style="border: 0; border-top: 1px solid #2E3030; margin: 0;">

                            {{-- Oil Life --}}
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
                                    <span style="color: #71717A; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Umur Oli (Oil Life)</span>
                                    <span style="color: #F4F4F5; font-weight: 700;">{{ $vehicle->oil_life_percentage ?? 100 }}%</span>
                                </div>
                                <div style="width: 100%; height: 6px; background-color: #1E2020; border-radius: 100px; overflow: hidden;">
                                    @php
                                        $oilScore = $vehicle->oil_life_percentage ?? 100;
                                        $oilColor = $oilScore >= 40 ? '#4ade80' : ($oilScore >= 15 ? '#fbbf24' : '#f87171');
                                    @endphp
                                    <div style="width: {{ $oilScore }}%; height: 100%; background-color: {{ $oilColor }}; border-radius: 100px;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Technical Specification List --}}
                        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #2E3030; padding-bottom: 8px;">
                                <span style="color: #71717A;">Warna</span>
                                <span style="font-weight: 600; color: #F4F4F5;">{{ $vehicle->color ?? '-' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #2E3030; padding-bottom: 8px;">
                                <span style="color: #71717A;">Nomor Rangka</span>
                                <span style="font-weight: 600; color: #F4F4F5; font-family: monospace;">{{ $vehicle->chassis_number ?? '-' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #71717A;">Nomor Mesin</span>
                                <span style="font-weight: 600; color: #F4F4F5; font-family: monospace;">{{ $vehicle->engine_number ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Digital ID QR Code Card --}}
                <div style="background: linear-gradient(145deg, #1C0A0C 0%, #2A0B0E 100%); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 20px; padding: 24px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <span style="display: inline-block; font-size: 10px; font-weight: 800; color: #F87171; letter-spacing: 0.05em; text-transform: uppercase; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 10px; border-radius: 100px; margin-bottom: 16px;">
                        Digital ID QR Code
                    </span>

                    {{-- QR Code Image Placeholder / Representation --}}
                    <div style="background: white; border-radius: 12px; padding: 12px; width: 140px; height: 140px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                        <div style="border: 3px solid #121414; width: 110px; height: 110px; position: relative; background-image: radial-gradient(#121414 20%, transparent 20%), radial-gradient(#121414 20%, transparent 20%); background-size: 8px 8px; background-position: 0 0, 4px 4px;">
                            {{-- Top-left corner --}}
                            <div style="position: absolute; top: 0; left: 0; width: 28px; height: 28px; border: 4px solid #121414; background: white; box-sizing: border-box;">
                                <div style="position: absolute; inset: 3px; background: #121414;"></div>
                            </div>
                            {{-- Top-right corner --}}
                            <div style="position: absolute; top: 0; right: 0; width: 28px; height: 28px; border: 4px solid #121414; background: white; box-sizing: border-box;">
                                <div style="position: absolute; inset: 3px; background: #121414;"></div>
                            </div>
                            {{-- Bottom-left corner --}}
                            <div style="position: absolute; bottom: 0; left: 0; width: 28px; height: 28px; border: 4px solid #121414; background: white; box-sizing: border-box;">
                                <div style="position: absolute; inset: 3px; background: #121414;"></div>
                            </div>
                        </div>
                    </div>

                    <h4 style="font-size: 15px; font-weight: 800; color: #F4F4F5; margin: 0 0 4px; font-family: monospace; letter-spacing: 0.05em;">{{ $vehicle->qr_code }}</h4>
                    <p style="color: #A1A1AA; font-size: 12px; margin: 0 0 16px; line-height: 1.4;">Scan stiker fisik kendaraan atau tunjukkan kode QR digital ini untuk memindai histori servis.</p>

                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('vehicles.qr.show', $vehicle) }}" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; background-color: #ff9aa4; border: 1px solid #ff9aa4; border-radius: 10px; color: #1A1C1C; font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='#ffb3bc';" onmouseout="this.style.backgroundColor='#ff9aa4';">
                            Buka Digital ID
                        </a>
                        <a href="{{ route('vehicles.qr.download', $vehicle) }}" style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; background-color: transparent; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; color: #F87171; text-decoration: none; cursor: pointer; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='rgba(239, 68, 68, 0.1)';" onmouseout="this.style.backgroundColor='transparent';" title="Unduh QR Code">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 20px; padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                    <span style="font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.05em;">Aksi Lainnya</span>
                    
                    <button onclick="alert('Fitur Transfer Kepemilikan dalam pengembangan (Epic 6)')" style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #252828; border: 1px solid #2E3030; border-radius: 12px; color: #A1A1AA; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 150ms;" onmouseover="this.style.borderColor='#ff9aa4';this.style.color='#ff9aa4';" onmouseout="this.style.borderColor='#2E3030';this.style.color='#A1A1AA';">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Transfer Kepemilikan
                        </div>
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

            </div>

            {{-- ==========================================
                 RIGHT COLUMN: Statistics & Timeline Tabs
                 ========================================== --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- Statistics Cards Row --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    {{-- Total Services --}}
                    <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px;">
                        <span style="display: block; font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Total Servis</span>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <span style="font-size: 28px; font-weight: 900; color: #F4F4F5; line-height: 1;">{{ $totalServices }}</span>
                            <span style="font-size: 13px; font-weight: 600; color: #71717A;">Kali</span>
                        </div>
                    </div>

                    {{-- Total Cost --}}
                    <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px;">
                        <span style="display: block; font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Total Biaya Servis</span>
                        <div style="display: flex; align-items: baseline; gap: 4px;">
                            <span style="font-size: 22px; font-weight: 900; color: #4ade80; line-height: 1;">
                                {{ 'Rp ' . number_format($totalCost, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Average Interval --}}
                    <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px;">
                        <span style="display: block; font-size: 11px; font-weight: 700; color: #71717A; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Rata-rata Interval</span>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <span style="font-size: 28px; font-weight: 900; color: #F4F4F5; line-height: 1;">
                                {{ $avgInterval ?? '-' }}
                            </span>
                            <span style="font-size: 13px; font-weight: 600; color: #71717A;">Hari</span>
                        </div>
                    </div>
                </div>

                {{-- Tabs & Activity Detail Card --}}
                <div style="background-color: #1E2020; border: 1px solid #2E3030; border-radius: 20px; padding: 24px;">
                    {{-- Tab Headers --}}
                    <div style="display: flex; gap: 8px; border-bottom: 1px solid #2E3030; padding-bottom: 12px; margin-bottom: 24px; overflow-x: auto; white-space: nowrap;">
                        <button id="tab-btn-timeline" onclick="switchTab('timeline')" style="padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; border: 1px solid #ff9aa4; background-color: rgba(255, 154, 164, 0.1); color: #ff9aa4; cursor: pointer; transition: all 150ms; outline: none;">
                            Timeline Servis
                        </button>
                        <button id="tab-btn-spareparts" onclick="switchTab('spareparts')" style="padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; border: 1px solid transparent; background-color: transparent; color: #A1A1AA; cursor: pointer; transition: all 150ms; outline: none;">
                            Daftar Sparepart
                        </button>
                    </div>

                    {{-- Tab Contents --}}
                    <div id="tab-content-timeline" style="display: block;">
                        @include('vehicles.partials.service-timeline')
                        @if($serviceRecords->isNotEmpty())
                            <div style="margin-top: 16px; text-align: center;">
                                <a href="{{ route('vehicles.service-history', $vehicle) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background-color: #252828; border: 1px solid #2E3030; border-radius: 8px; color: #F4F4F5; font-size: 13px; font-weight: 600; text-decoration: none; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='#2A2D2D';" onmouseout="this.style.backgroundColor='#252828';">
                                    Lihat Riwayat Lengkap & Filter
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div id="tab-content-spareparts" style="display: none;">
                        @include('vehicles.partials.spareparts-list')
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Tabs Client Script --}}
    <script>
        function switchTab(tab) {
            const timelineBtn = document.getElementById('tab-btn-timeline');
            const sparepartsBtn = document.getElementById('tab-btn-spareparts');
            const timelineContent = document.getElementById('tab-content-timeline');
            const sparepartsContent = document.getElementById('tab-content-spareparts');

            if (tab === 'timeline') {
                timelineBtn.style.border = '1px solid #ff9aa4';
                timelineBtn.style.backgroundColor = 'rgba(255, 154, 164, 0.1)';
                timelineBtn.style.color = '#ff9aa4';

                sparepartsBtn.style.border = '1px solid transparent';
                sparepartsBtn.style.backgroundColor = 'transparent';
                sparepartsBtn.style.color = '#A1A1AA';

                timelineContent.style.display = 'block';
                sparepartsContent.style.display = 'none';
            } else if (tab === 'spareparts') {
                sparepartsBtn.style.border = '1px solid #ff9aa4';
                sparepartsBtn.style.backgroundColor = 'rgba(255, 154, 164, 0.1)';
                sparepartsBtn.style.color = '#ff9aa4';

                timelineBtn.style.border = '1px solid transparent';
                timelineBtn.style.backgroundColor = 'transparent';
                timelineBtn.style.color = '#A1A1AA';

                sparepartsContent.style.display = 'block';
                timelineContent.style.display = 'none';
            }
        }
    </script>
</x-app-layout>
