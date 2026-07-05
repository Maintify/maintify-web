<x-app-layout>
    @slot('pageTitle', 'Tambah Kendaraan')

    <div style="max-width: 680px; margin: 0 auto; padding: 24px 16px;">
        {{-- Header --}}
        <div style="margin-bottom: 28px;">
            <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; color: #71717A; text-decoration: none; font-size: 13px; font-weight: 500; transition: color 150ms;" onmouseover="this.style.color='#F4F4F5'" onmouseout="this.style.color='#71717A'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
            <h1 style="font-size: 24px; font-weight: 800; color: #F4F4F5; letter-spacing: -0.02em; margin-top: 12px; margin-bottom: 6px;">Daftarkan Kendaraan Baru 🏍️</h1>
            <p style="color: #71717A; font-size: 14px; margin: 0;">Lengkapi data di bawah ini untuk mendaftarkan kendaraan dan menerbitkan QR Code unik.</p>
        </div>

        {{-- Form Card --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
            <form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf

                {{-- Photo Upload --}}
                <div class="form-group">
                    <label for="photo" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">
                        Foto Kendaraan <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Should Have - JPG/PNG, Maks 5MB)</span>
                    </label>
                    <input
                        id="photo"
                        type="file"
                        name="photo"
                        accept="image/png, image/jpeg, image/jpg"
                        class="form-input @error('photo') form-input-error @enderror"
                        style="padding: 10px; background-color: #252828; color: #F4F4F5;"
                    />
                    @error('photo')
                        <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    {{-- Brand --}}
                    <div class="form-group">
                        <label for="brand" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Merek *</label>
                        <input
                            id="brand"
                            type="text"
                            name="brand"
                            value="{{ old('brand') }}"
                            required
                            placeholder="Contoh: Honda, Yamaha"
                            class="form-input @error('brand') form-input-error @enderror"
                        />
                        @error('brand')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Model --}}
                    <div class="form-group">
                        <label for="model" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Model *</label>
                        <input
                            id="model"
                            type="text"
                            name="model"
                            value="{{ old('model') }}"
                            required
                            placeholder="Contoh: Vario 160, NMAX"
                            class="form-input @error('model') form-input-error @enderror"
                        />
                        @error('model')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    {{-- Type / Varian --}}
                    <div class="form-group">
                        <label for="type" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Tipe / Varian <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Opsional)</span></label>
                        <input
                            id="type"
                            type="text"
                            name="type"
                            value="{{ old('type') }}"
                            placeholder="Contoh: CBS, ABS, Standard"
                            class="form-input @error('type') form-input-error @enderror"
                        />
                        @error('type')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Year --}}
                    <div class="form-group">
                        <label for="year" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Tahun Pembuatan *</label>
                        <input
                            id="year"
                            type="number"
                            name="year"
                            value="{{ old('year') }}"
                            required
                            min="1900"
                            max="{{ date('Y') + 1 }}"
                            placeholder="Contoh: 2023"
                            class="form-input @error('year') form-input-error @enderror"
                        />
                        @error('year')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    {{-- Plate Number --}}
                    <div class="form-group">
                        <label for="plate_number" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Nomor Plat (No. Polisi) *</label>
                        <input
                            id="plate_number"
                            type="text"
                            name="plate_number"
                            value="{{ old('plate_number') }}"
                            required
                            placeholder="Contoh: B 1234 ABC"
                            class="form-input @error('plate_number') form-input-error @enderror"
                        />
                        @error('plate_number')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div class="form-group">
                        <label for="color" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Warna <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Opsional)</span></label>
                        <input
                            id="color"
                            type="text"
                            name="color"
                            value="{{ old('color') }}"
                            placeholder="Contoh: Hitam, Merah, Putih"
                            class="form-input @error('color') form-input-error @enderror"
                        />
                        @error('color')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    {{-- Fuel Type --}}
                    <div class="form-group">
                        <label for="fuel_type" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Bahan Bakar *</label>
                        <select
                            id="fuel_type"
                            name="fuel_type"
                            required
                            class="form-input @error('fuel_type') form-input-error @enderror"
                            style="background-color: #252828; color: #F4F4F5;"
                        >
                            <option value="gasoline" {{ old('fuel_type') === 'gasoline' ? 'selected' : '' }}>Bensin (Gasoline)</option>
                            <option value="diesel" {{ old('fuel_type') === 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ old('fuel_type') === 'electric' ? 'selected' : '' }}>Listrik (Electric)</option>
                            <option value="hybrid" {{ old('fuel_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('fuel_type')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Initial Odometer --}}
                    <div class="form-group">
                        <label for="current_odometer" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Odometer Awal (Km) *</label>
                        <input
                            id="current_odometer"
                            type="number"
                            name="current_odometer"
                            value="{{ old('current_odometer', 0) }}"
                            required
                            min="0"
                            placeholder="Mulai dari: 0"
                            class="form-input @error('current_odometer') form-input-error @enderror"
                        />
                        @error('current_odometer')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    {{-- VIN (Chassis Number) --}}
                    <div class="form-group">
                        <label for="chassis_number" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Nomor VIN (Rangka) * <span style="color: #71717A; font-weight: 400; font-size: 11px;">(17 Karakter)</span></label>
                        <input
                            id="chassis_number"
                            type="text"
                            name="chassis_number"
                            value="{{ old('chassis_number') }}"
                            required
                            maxlength="17"
                            placeholder="Masukkan 17 karakter VIN"
                            class="form-input @error('chassis_number') form-input-error @enderror"
                            style="text-transform: uppercase;"
                        />
                        @error('chassis_number')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Engine Number --}}
                    <div class="form-group">
                        <label for="engine_number" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Nomor Mesin <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Opsional)</span></label>
                        <input
                            id="engine_number"
                            type="text"
                            name="engine_number"
                            value="{{ old('engine_number') }}"
                            placeholder="Contoh: JF51E-123456"
                            class="form-input @error('engine_number') form-input-error @enderror"
                            style="text-transform: uppercase;"
                        />
                        @error('engine_number')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 13px; border-radius: 12px; justify-content: center; font-size: 14px; font-weight: 600;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Simpan & Terbitkan QR Code
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn-secondary" style="padding: 13px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px; font-weight: 600; border: 1px solid #2E3030; color: #A1A1AA; background: transparent; transition: all 150ms;" onmouseover="this.style.color='#F4F4F5';this.style.borderColor='#A1A1AA'" onmouseout="this.style.color='#A1A1AA';this.style.borderColor='#2E3030'">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
