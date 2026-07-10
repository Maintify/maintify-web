<x-app-layout>
    @slot('pageTitle', 'Edit Kendaraan')

    <div style="max-width: 680px; margin: 0 auto; padding: 24px 16px;">
        {{-- Header --}}
        <div style="margin-bottom: 28px;">
            <a href="{{ route('vehicles.show', $vehicle) }}" style="display: inline-flex; align-items: center; gap: 8px; color: #71717A; text-decoration: none; font-size: 13px; font-weight: 500; transition: color 150ms;" onmouseover="this.style.color='#F4F4F5'" onmouseout="this.style.color='#71717A'">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Detail Kendaraan
            </a>
            <h1 style="font-size: 24px; font-weight: 800; color: #F4F4F5; letter-spacing: -0.02em; margin-top: 12px; margin-bottom: 6px;">Edit Data Kendaraan ✏️</h1>
            <p style="color: #71717A; font-size: 14px; margin: 0;">Perbarui informasi kendaraan. Plat nomor dan nomor rangka (VIN) tidak dapat diubah setelah terdaftar.</p>
        </div>

        {{-- Form Card --}}
        <div style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
            <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf
                @method('PUT')

                {{-- Photo Upload --}}
                <div class="form-group">
                    <label for="photo" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">
                        Foto Kendaraan <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Biarkan kosong jika tidak ingin mengubah)</span>
                    </label>
                    
                    @if($vehicle->photo_url)
                        <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
                            <img src="{{ $vehicle->photo_url }}" alt="Current Photo" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #2E3030;">
                            <span style="color: #A1A1AA; font-size: 12px;">Foto saat ini</span>
                        </div>
                    @endif

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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Plate Number (Read Only) --}}
                    <div class="form-group">
                        <label for="plate_number" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #71717A; margin-bottom: 8px;">Nomor Plat (No. Polisi) <span style="font-weight: 400; font-size: 11px;">(Tidak dapat diubah)</span></label>
                        <input
                            id="plate_number"
                            type="text"
                            value="{{ $vehicle->plate_number }}"
                            readonly
                            disabled
                            class="form-input"
                            style="background-color: #111111; color: #71717A; cursor: not-allowed; opacity: 0.8;"
                        />
                    </div>

                    {{-- VIN (Chassis Number) (Read Only) --}}
                    <div class="form-group">
                        <label for="chassis_number" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #71717A; margin-bottom: 8px;">Nomor VIN (Rangka) <span style="font-weight: 400; font-size: 11px;">(Tidak dapat diubah)</span></label>
                        <input
                            id="chassis_number"
                            type="text"
                            value="{{ $vehicle->chassis_number }}"
                            readonly
                            disabled
                            class="form-input"
                            style="text-transform: uppercase; background-color: #111111; color: #71717A; cursor: not-allowed; opacity: 0.8;"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Brand --}}
                    <div class="form-group">
                        <label for="brand" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Merek *</label>
                        <input
                            id="brand"
                            type="text"
                            name="brand"
                            value="{{ old('brand', $vehicle->brand) }}"
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
                            value="{{ old('model', $vehicle->model) }}"
                            required
                            placeholder="Contoh: Vario 160, NMAX"
                            class="form-input @error('model') form-input-error @enderror"
                        />
                        @error('model')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Type / Varian --}}
                    <div class="form-group">
                        <label for="type" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Tipe / Varian <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Opsional)</span></label>
                        <input
                            id="type"
                            type="text"
                            name="type"
                            value="{{ old('type', $vehicle->type) }}"
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
                            value="{{ old('year', $vehicle->year) }}"
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Color --}}
                    <div class="form-group">
                        <label for="color" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Warna <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Opsional)</span></label>
                        <input
                            id="color"
                            type="text"
                            name="color"
                            value="{{ old('color', $vehicle->color) }}"
                            placeholder="Contoh: Hitam, Merah, Putih"
                            class="form-input @error('color') form-input-error @enderror"
                        />
                        @error('color')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

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
                            <option value="gasoline" {{ old('fuel_type', $vehicle->fuel_type) === 'gasoline' ? 'selected' : '' }}>Bensin (Gasoline)</option>
                            <option value="diesel" {{ old('fuel_type', $vehicle->fuel_type) === 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ old('fuel_type', $vehicle->fuel_type) === 'electric' ? 'selected' : '' }}>Listrik (Electric)</option>
                            <option value="hybrid" {{ old('fuel_type', $vehicle->fuel_type) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('fuel_type')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Engine Number --}}
                    <div class="form-group">
                        <label for="engine_number" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Nomor Mesin <span style="color: #71717A; font-weight: 400; font-size: 11px;">(Opsional)</span></label>
                        <input
                            id="engine_number"
                            type="text"
                            name="engine_number"
                            value="{{ old('engine_number', $vehicle->engine_number) }}"
                            placeholder="Contoh: JF51E-123456"
                            class="form-input @error('engine_number') form-input-error @enderror"
                            style="text-transform: uppercase;"
                        />
                        @error('engine_number')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Current Odometer --}}
                    <div class="form-group">
                        <label for="current_odometer" class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #F4F4F5; margin-bottom: 8px;">Odometer (Km) *</label>
                        <input
                            id="current_odometer"
                            type="number"
                            name="current_odometer"
                            value="{{ old('current_odometer', $vehicle->current_odometer) }}"
                            required
                            min="0"
                            class="form-input @error('current_odometer') form-input-error @enderror"
                        />
                        @error('current_odometer')
                            <span style="color: #F87171; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 13px; border-radius: 12px; justify-content: center; font-size: 14px; font-weight: 600;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-secondary" style="padding: 13px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px; font-weight: 600; border: 1px solid #2E3030; color: #A1A1AA; background: transparent; transition: all 150ms;" onmouseover="this.style.color='#F4F4F5';this.style.borderColor='#A1A1AA'" onmouseout="this.style.color='#A1A1AA';this.style.borderColor='#2E3030'">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
