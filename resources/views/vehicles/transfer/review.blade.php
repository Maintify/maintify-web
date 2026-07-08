<x-app-layout>
    @slot('pageTitle', 'Konfirmasi Transfer Kendaraan')

    <div style="max-width: 720px; margin: 0 auto; padding: 24px 16px;">
        {{-- Navigation --}}
        <div style="margin-bottom: 24px;">
            <a href="{{ route('vehicles.show', $vehicle) }}" style="display: inline-flex; align-items: center; gap: 8px; color: #71717A; text-decoration: none; font-size: 13px; font-weight: 600; transition: color 150ms;" onmouseover="this.style.color='#F4F4F5'" onmouseout="this.style.color='#71717A'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Detail Kendaraan
            </a>
        </div>

        {{-- Page Header --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.2); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h1 style="font-size: 20px; font-weight: 800; color: #F4F4F5; margin: 0; line-height: 1.3;">Konfirmasi Akhir</h1>
                    <p style="font-size: 13px; color: #71717A; margin: 0;">Penerima telah menyetujui, silakan lakukan konfirmasi akhir.</p>
                </div>
            </div>
        </div>

        {{-- Error State --}}
        @if (session('error'))
            <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                <div style="background: rgba(239, 68, 68, 0.15); border-radius: 50%; padding: 6px; display: flex; align-items: center; justify-content: center; color: #f87171; flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 style="color: #f87171; font-size: 14px; font-weight: 600; margin: 0 0 2px;">Terjadi Kesalahan</h4>
                    <p style="color: #A1A1AA; font-size: 13px; margin: 0;">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Vehicle Summary Card --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px; margin-bottom: 24px;">
            <span style="display: block; font-size: 10px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px;">Kendaraan Yang Ditransfer</span>

            <div style="display: flex; gap: 16px; align-items: center;">
                <div style="width: 60px; height: 60px; border-radius: 12px; background-color: #252828; border: 1px solid #2E3030; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                    @if($vehicle->photo_url)
                        <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <svg style="width: 24px; height: 24px; color: #71717A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zm0 0h4l3-3V8h-7v8z"/>
                        </svg>
                    @endif
                </div>

                <div style="flex: 1; min-width: 0;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #F4F4F5; margin: 0 0 4px; line-height: 1.3;">
                        {{ $vehicle->brand }} {{ $vehicle->model }}
                    </h3>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="font-size: 13px; font-weight: 700; color: #ff9aa4; font-family: monospace; letter-spacing: 0.02em;">
                            {{ $vehicle->plate_number }}
                        </span>
                        <span style="color: #3A3D3D;">•</span>
                        <span style="font-size: 12px; font-weight: 500; color: #71717A;">
                            VIN: {{ $vehicle->chassis_number }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recipient Info Card --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px; margin-bottom: 24px;">
            <span style="display: block; font-size: 10px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px;">Pemilik Baru (Penerima)</span>

            <div style="display: flex; gap: 16px; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #252828; border: 1px solid #2E3030; display: flex; align-items: center; justify-content: center; color: #F4F4F5; font-weight: 700;">
                    {{ strtoupper(substr($recipient->name, 0, 1)) }}
                </div>
                <div>
                    <h4 style="font-size: 14px; font-weight: 700; color: #F4F4F5; margin: 0 0 2px;">{{ $recipient->name }}</h4>
                    <p style="font-size: 13px; color: #A1A1AA; margin: 0;">{{ $recipient->email }}</p>
                </div>
            </div>
        </div>

        {{-- Confirmation Form --}}
        <form action="{{ route('transfers.confirm', $transfer) }}" method="POST">
            @csrf

            <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 16px; padding: 24px; margin-bottom: 24px;">
                <h4 style="font-size: 15px; font-weight: 800; color: #f87171; margin: 0 0 16px;">Pernyataan Finalisasi Transfer</h4>
                
                <p style="font-size: 13px; color: #D4D4D8; line-height: 1.6; margin: 0 0 20px;">
                    Anda akan secara <strong>permanen</strong> mentransfer hak akses dan seluruh data kendaraan (termasuk riwayat servis lengkap) kepada <strong>{{ $recipient->name }}</strong>. 
                    <br><br>
                    Setelah Anda menekan tombol konfirmasi, Anda <strong>tidak akan lagi memiliki akses</strong> ke kendaraan ini melalui dashboard Anda. Aksi ini tidak dapat dibatalkan melalui sistem.
                </p>

                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <input type="checkbox" id="disclaimer_agreed" name="disclaimer_agreed" value="1" style="margin-top: 3px; width: 18px; height: 18px; accent-color: #ff9aa4;" required {{ old('disclaimer_agreed') ? 'checked' : '' }}>
                    <label for="disclaimer_agreed" style="font-size: 13px; font-weight: 500; color: #F4F4F5; cursor: pointer; user-select: none; line-height: 1.5;">
                        Saya menyatakan setuju untuk memindahkan kepemilikan kendaraan secara permanen. Seluruh data kendaraan dan riwayat servis akan menjadi hak milik penerima sepenuhnya. Saya memahami bahwa aksi ini bersifat final dan tidak dapat dibatalkan.
                    </label>
                </div>

                @error('disclaimer_agreed')
                    <div style="margin-top: 10px; font-size: 12px; color: #f87171; margin-left: 30px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px; background-color: #f87171; border: none; border-radius: 12px; color: #ffffff; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 150ms; outline: none;" onmouseover="this.style.backgroundColor='#ef4444';" onmouseout="this.style.backgroundColor='#f87171';">
                Konfirmasi & Lepas Kepemilikan
            </button>
        </form>

    </div>
</x-app-layout>
