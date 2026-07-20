<x-app-layout>
    @slot('pageTitle', 'Digital ID QR Code')

    <div style="max-width: 500px; margin: 0 auto; padding: 24px 16px;">
        {{-- Header --}}
        <div style="margin-bottom: 28px;">
            <a href="{{ route('vehicles.show', $vehicle) }}" style="display: inline-flex; align-items: center; gap: 8px; color: #71717A; text-decoration: none; font-size: 13px; font-weight: 500; transition: color 150ms;" onmouseover="this.style.color='#F4F4F5'" onmouseout="this.style.color='#71717A'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Detail Kendaraan
            </a>
            <h1 style="font-size: 24px; font-weight: 800; color: #F4F4F5; letter-spacing: -0.02em; margin-top: 12px; margin-bottom: 6px;">Digital ID QR Code </h1>
            <p style="color: #A1A1AA; font-size: 14px; margin: 0;">Identitas digital untuk keperluan verifikasi dan histori servis di jaringan bengkel mitra Maintify.</p>
        </div>

        {{-- Security Banner (FR-043) --}}
        <div style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; padding: 12px 16px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px;">
            <svg style="width: 20px; height: 20px; color: #F59E0B; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <strong style="color: #FCD34D; font-size: 13px; display: block; margin-bottom: 2px;">Jaga Kerahasiaan QR Code!</strong>
                <p style="color: #D4D4D8; font-size: 12px; margin: 0; line-height: 1.5;">QR Code ini bersifat unik dan memberikan akses ke histori servis kendaraan Anda. Jangan membagikannya kepada pihak yang tidak berkepentingan.</p>
            </div>
        </div>

        {{-- QR Code Card --}}
        <div style="background: linear-gradient(145deg, #1C0A0C 0%, #2A0B0E 100%); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 20px; padding: 32px 24px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.4);">
            
            {{-- Vehicle Basic Info --}}
            <div style="margin-bottom: 24px;">
                <h2 style="color: #F4F4F5; font-size: 20px; font-weight: 800; margin: 0 0 4px;">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
                <span style="display: inline-block; background-color: #2E3030; color: #F4F4F5; padding: 4px 12px; border-radius: 6px; font-size: 14px; font-weight: 700; font-family: monospace; letter-spacing: 2px;">
                    {{ $vehicle->plate_number }}
                </span>
            </div>

            {{-- Actual QR Code Image --}}
            <div style="background: white; border-radius: 16px; padding: 16px; width: 220px; height: 220px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                @if($vehicle->qr_code_url)
                    <img src="{{ asset($vehicle->qr_code_url) }}" alt="QR Code {{ $vehicle->plate_number }}" style="width: 100%; height: 100%; object-fit: contain;">
                @else
                    <div style="color: #F87171; font-size: 13px; font-weight: 600;">QR Code Tidak Tersedia</div>
                @endif
            </div>

            {{-- Token & Verification Status --}}
            <div style="margin-bottom: 32px;">
                <div style="font-size: 16px; font-weight: 800; color: #F87171; font-family: monospace; letter-spacing: 0.1em; margin-bottom: 8px;">
                    {{ $vehicle->qr_code }}
                </div>
                
                @php
                    $activeQr = $vehicle->activeQrCode();
                    $isActive = $activeQr ? true : false;
                @endphp
                <div style="display: inline-flex; align-items: center; gap: 6px; background-color: {{ $isActive ? 'rgba(74, 222, 128, 0.1)' : 'rgba(248, 113, 113, 0.1)' }}; border: 1px solid {{ $isActive ? 'rgba(74, 222, 128, 0.2)' : 'rgba(248, 113, 113, 0.2)' }}; padding: 4px 12px; border-radius: 100px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $isActive ? '#4ade80' : '#f87171' }}; box-shadow: 0 0 8px {{ $isActive ? '#4ade80' : '#f87171' }};"></span>
                    <span style="color: {{ $isActive ? '#4ade80' : '#f87171' }}; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                        Status: {{ $isActive ? 'Verified / Aktif' : 'Unverified / Tidak Aktif' }}
                    </span>
                </div>
            </div>

            {{-- Vehicle Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 p-4 bg-black/20 rounded-xl text-left">
                <div>
                    <span style="display: block; color: #71717A; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Nomor Rangka (VIN)</span>
                    <span style="display: block; color: #E4E4E7; font-size: 13px; font-weight: 600; font-family: monospace;">{{ $vehicle->chassis_number }}</span>
                </div>
                <div>
                    <span style="display: block; color: #71717A; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Nomor Mesin</span>
                    <span style="display: block; color: #E4E4E7; font-size: 13px; font-weight: 600; font-family: monospace;">{{ $vehicle->engine_number ?: '-' }}</span>
                </div>
                <div>
                    <span style="display: block; color: #71717A; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Tahun</span>
                    <span style="display: block; color: #E4E4E7; font-size: 13px; font-weight: 600;">{{ $vehicle->year }}</span>
                </div>
                <div>
                    <span style="display: block; color: #71717A; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Warna</span>
                    <span style="display: block; color: #E4E4E7; font-size: 13px; font-weight: 600;">{{ $vehicle->color ?: '-' }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('vehicles.qr.download', $vehicle) }}" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background-color: #ff9aa4; color: #1A1C1C; border-radius: 12px; font-size: 14px; font-weight: 700; text-decoration: none; transition: background-color 150ms;" onmouseover="this.style.backgroundColor='#ffb3bc';" onmouseout="this.style.backgroundColor='#ff9aa4';">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh (Save)
                </a>
                
                {{-- Action: Regenerate QR --}}
                <form action="{{ route('vehicles.qr.regenerate', $vehicle) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membuat ulang QR Code? QR Code yang lama akan hangus dan tidak dapat digunakan lagi.');" style="display: inline-block;">
                    @csrf
                    <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px; background-color: transparent; border: 1px solid rgba(239, 68, 68, 0.3); color: #F87171; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 150ms;" onmouseover="this.style.backgroundColor='rgba(239, 68, 68, 0.1)';" onmouseout="this.style.backgroundColor='transparent';" title="Buat Ulang (Regenerate)">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
