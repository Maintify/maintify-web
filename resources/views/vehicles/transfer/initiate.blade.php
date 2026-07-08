<x-app-layout>
    @slot('pageTitle', 'Transfer Kepemilikan Kendaraan')

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
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 154, 164, 0.1); border: 1px solid rgba(255, 154, 164, 0.2); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #ff9aa4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 style="font-size: 20px; font-weight: 800; color: #F4F4F5; margin: 0; line-height: 1.3;">Transfer Kepemilikan</h1>
                    <p style="font-size: 13px; color: #71717A; margin: 0;">Pindahkan kendaraan ini ke pemilik baru</p>
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
                    <h4 style="color: #f87171; font-size: 14px; font-weight: 600; margin: 0 0 2px;">Tidak Dapat Melanjutkan</h4>
                    <p style="color: #A1A1AA; font-size: 13px; margin: 0;">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Vehicle Summary Card --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px; margin-bottom: 24px;">
            <span style="display: block; font-size: 10px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px;">Kendaraan Yang Akan Ditransfer</span>

            <div style="display: flex; gap: 16px; align-items: center;">
                {{-- Vehicle Photo --}}
                <div style="width: 80px; height: 80px; border-radius: 12px; background-color: #252828; border: 1px solid #2E3030; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                    @if($vehicle->photo_url)
                        <img src="{{ $vehicle->photo_url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <svg style="width: 32px; height: 32px; color: #71717A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zm0 0h4l3-3V8h-7v8z"/>
                        </svg>
                    @endif
                </div>

                {{-- Vehicle Details --}}
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
                            Tahun {{ $vehicle->year }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                        <span style="font-size: 11px; font-weight: 600; color: #71717A; text-transform: uppercase; letter-spacing: 0.03em;">Odometer:</span>
                        <span style="font-size: 12px; font-weight: 700; color: #F4F4F5;">{{ number_format($vehicle->current_odometer) }} Km</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning Banner --}}
        <div style="background: linear-gradient(145deg, rgba(251, 191, 36, 0.06) 0%, rgba(251, 191, 36, 0.02) 100%); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; gap: 12px;">
            <div style="flex-shrink: 0; margin-top: 2px;">
                <svg style="width: 20px; height: 20px; color: #fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <h4 style="font-size: 13px; font-weight: 700; color: #fbbf24; margin: 0 0 4px;">Perhatian — Baca Sebelum Melanjutkan</h4>
                <ul style="margin: 0; padding-left: 16px; font-size: 12px; color: #A1A1AA; line-height: 1.8;">
                    <li>Transfer kepemilikan bersifat <strong style="color: #F4F4F5;">permanen</strong> dan tidak dapat dibatalkan setelah dikonfirmasi.</li>
                    <li>Penerima harus memiliki akun Maintify yang terdaftar.</li>
                    <li>Penerima akan menerima notifikasi dan harus menyetujui permintaan ini.</li>
                    <li>Permintaan transfer akan <strong style="color: #F4F4F5;">kedaluwarsa dalam 7 hari</strong> jika tidak ada respons.</li>
                    <li>Seluruh riwayat servis kendaraan akan ikut berpindah ke pemilik baru.</li>
                </ul>
            </div>
        </div>

        {{-- Transfer Form --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 24px;">
            <span style="display: block; font-size: 10px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 20px;">Identifikasi Penerima</span>

            <form action="{{ route('vehicles.transfer.store', $vehicle) }}" method="POST" id="transfer-form">
                @csrf

                {{-- Recipient Identifier Input --}}
                <div style="margin-bottom: 20px;">
                    <label for="recipient_identifier" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">
                        Email atau Nomor Telepon Penerima
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #71717A; pointer-events: none;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="recipient_identifier"
                            name="recipient_identifier"
                            value="{{ old('recipient_identifier') }}"
                            placeholder="Contoh: user@email.com atau 08123456789"
                            autocomplete="off"
                            style="width: 100%; padding: 12px 14px 12px 42px; background-color: #252828; border: 1px solid {{ $errors->has('recipient_identifier') ? 'rgba(239, 68, 68, 0.5)' : '#2E3030' }}; border-radius: 10px; color: #F4F4F5; font-size: 14px; font-weight: 500; outline: none; transition: border-color 150ms; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#ff9aa4'"
                            onblur="this.style.borderColor='{{ $errors->has('recipient_identifier') ? 'rgba(239, 68, 68, 0.5)' : '#2E3030' }}'"
                        >
                    </div>

                    {{-- Validation Error --}}
                    @error('recipient_identifier')
                        <div style="display: flex; align-items: flex-start; gap: 6px; margin-top: 8px; padding: 10px 12px; background: rgba(239, 68, 68, 0.06); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 8px;">
                            <svg style="width: 16px; height: 16px; color: #f87171; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span style="font-size: 12px; color: #f87171; line-height: 1.5;">{{ $message }}</span>
                        </div>
                    @enderror

                    <p style="font-size: 11px; color: #52525B; margin: 10px 0 0; line-height: 1.5;">
                        Masukkan alamat email atau nomor telepon yang terdaftar di akun Maintify penerima.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 12px; padding-top: 4px;">
                    <a href="{{ route('vehicles.show', $vehicle) }}" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 12px 20px; background-color: #252828; border: 1px solid #2E3030; border-radius: 10px; color: #A1A1AA; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 150ms; cursor: pointer;" onmouseover="this.style.backgroundColor='#2A2D2D'; this.style.color='#F4F4F5';" onmouseout="this.style.backgroundColor='#252828'; this.style.color='#A1A1AA';">
                        Batal
                    </a>
                    <button type="submit" id="submit-transfer-btn" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; background-color: #ff9aa4; border: 1px solid #ff9aa4; border-radius: 10px; color: #1A1C1C; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 150ms; outline: none;" onmouseover="this.style.backgroundColor='#ffb3bc'; this.style.borderColor='#ffb3bc';" onmouseout="this.style.backgroundColor='#ff9aa4'; this.style.borderColor='#ff9aa4';">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Kirim Permintaan Transfer
                    </button>
                </div>
            </form>
        </div>

        {{-- Transfer Flow Info --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 20px; margin-top: 24px;">
            <span style="display: block; font-size: 10px; font-weight: 700; color: #71717A; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 16px;">Bagaimana Proses Transfer Bekerja</span>

            <div style="display: flex; flex-direction: column; gap: 0;">
                {{-- Step 1 --}}
                <div style="display: flex; gap: 14px;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(255, 154, 164, 0.15); border: 2px solid #ff9aa4; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #ff9aa4; flex-shrink: 0;">1</div>
                        <div style="width: 2px; flex: 1; background-color: #2E3030; margin: 4px 0;"></div>
                    </div>
                    <div style="padding-bottom: 20px;">
                        <h4 style="font-size: 13px; font-weight: 700; color: #F4F4F5; margin: 0 0 2px;">Kirim Permintaan</h4>
                        <p style="font-size: 12px; color: #71717A; margin: 0; line-height: 1.5;">Anda mengirim permintaan transfer dengan mengisi email/telepon penerima.</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div style="display: flex; gap: 14px;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(161, 161, 170, 0.1); border: 2px solid #52525B; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #71717A; flex-shrink: 0;">2</div>
                        <div style="width: 2px; flex: 1; background-color: #2E3030; margin: 4px 0;"></div>
                    </div>
                    <div style="padding-bottom: 20px;">
                        <h4 style="font-size: 13px; font-weight: 700; color: #A1A1AA; margin: 0 0 2px;">Persetujuan Penerima</h4>
                        <p style="font-size: 12px; color: #71717A; margin: 0; line-height: 1.5;">Penerima meninjau permintaan dan menyetujui atau menolak transfer.</p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div style="display: flex; gap: 14px;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(161, 161, 170, 0.1); border: 2px solid #52525B; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #71717A; flex-shrink: 0;">3</div>
                    </div>
                    <div>
                        <h4 style="font-size: 13px; font-weight: 700; color: #A1A1AA; margin: 0 0 2px;">Konfirmasi & Selesai</h4>
                        <p style="font-size: 12px; color: #71717A; margin: 0; line-height: 1.5;">Anda mengonfirmasi transfer secara final. Kepemilikan berpindah secara permanen.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
