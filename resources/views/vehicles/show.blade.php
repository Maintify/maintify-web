<x-app-layout>
    @slot('pageTitle', 'Detail Kendaraan')

    <div style="max-width: 680px; margin: 0 auto; padding: 24px 16px;">
        {{-- Navigation & Header --}}
        <div style="margin-bottom: 24px;">
            <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; color: #71717A; text-decoration: none; font-size: 13px; font-weight: 500; transition: color 150ms;" onmouseover="this.style.color='#F4F4F5'" onmouseout="this.style.color='#71717A'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
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
                    <h4 style="color: #4ade80; font-size: 14px; font-weight: 600; margin: 0 0 2px;">Pendaftaran Berhasil!</h4>
                    <p style="color: #A1A1AA; font-size: 13px; margin: 0;">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Main Grid --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
            {{-- QR Code Card (Digital ID) --}}
            <div style="background: linear-gradient(145deg, #1C0A0C 0%, #2A0B0E 100%); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 20px; padding: 32px 24px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 100px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 24px;">
                    <span style="font-size: 11px; font-weight: 700; color: #F87171; letter-spacing: 0.05em; text-transform: uppercase;">Digital ID QR Code</span>
                </div>

                {{-- QR Placeholder Box --}}
                <div style="background: white; border-radius: 16px; padding: 16px; width: 200px; height: 200px; margin: 0 auto 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.5);">
                    {{-- Decorative representation of QR code --}}
                    <div style="border: 4px solid #121414; width: 160px; height: 160px; position: relative; background-image: radial-gradient(#121414 20%, transparent 20%), radial-gradient(#121414 20%, transparent 20%); background-size: 10px 10px; background-position: 0 0, 5px 5px;">
                        {{-- Corner boxes --}}
                        <div style="position: absolute; top: 0; left: 0; width: 40px; height: 40px; border: 6px solid #121414; background: white; box-sizing: border-box;">
                            <div style="position: absolute; inset: 4px; background: #121414;"></div>
                        </div>
                        <div style="position: absolute; top: 0; right: 0; width: 40px; height: 40px; border: 6px solid #121414; background: white; box-sizing: border-box;">
                            <div style="position: absolute; inset: 4px; background: #121414;"></div>
                        </div>
                        <div style="position: absolute; bottom: 0; left: 0; width: 40px; height: 40px; border: 6px solid #121414; background: white; box-sizing: border-box;">
                            <div style="position: absolute; inset: 4px; background: #121414;"></div>
                        </div>
                    </div>
                </div>

                <h3 style="font-size: 20px; font-weight: 800; color: #F4F4F5; margin: 0 0 6px;">{{ $vehicle->qr_code }}</h3>
                <p style="color: #71717A; font-size: 13px; margin: 0 0 20px;">Tunjukkan QR Code ini ke petugas bengkel mitra untuk memindai histori servis.</p>

                <button class="btn-primary" style="padding: 10px 20px; font-size: 13px; font-weight: 600; border-radius: 10px; margin: 0 auto; display: inline-flex; align-items: center; gap: 8px;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh QR Code
                </button>
            </div>

            {{-- Vehicle Details Card --}}
            <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 20px; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #F4F4F5; margin: 0 0 16px; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 20px; height: 20px; color: #ff9aa4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                    </svg>
                    Spesifikasi Kendaraan
                </h3>

                {{-- Detail Fields --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Merek</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5;">{{ $vehicle->brand }}</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Model</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5;">{{ $vehicle->model }}</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Plat Nomor</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5;">{{ $vehicle->plate_number }}</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Tahun Pembuatan</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5;">{{ $vehicle->year }}</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Bahan Bakar</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5;">{{ ucfirst($vehicle->fuel_type) }}</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Odometer Awal</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5;">{{ number_format($vehicle->current_odometer) }} Km</span>
                    </div>
                    <div style="grid-column: span 2;">
                        <span style="display: block; font-size: 11px; color: #71717A; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Nomor VIN (Rangka)</span>
                        <span style="font-size: 14px; font-weight: 600; color: #F4F4F5; font-family: monospace;">{{ $vehicle->chassis_number }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
