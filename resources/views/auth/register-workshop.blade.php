<x-guest-layout>
    @section('title', 'Daftar Bengkel Mitra')
 
    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Daftar Bengkel Mitra 🔧</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">Daftarkan bengkel Anda ke platform Maintify</p>
    </div>
 
    <div x-data="{
        step: {{ $errors->hasAny(['owner_name', 'email', 'owner_ktp_number']) ? 1 : ($errors->hasAny(['workshop_name', 'phone', 'address', 'city', 'province', 'operational_hours']) ? 2 : ($errors->hasAny(['legal_document', 'password', 'password_confirmation']) ? 3 : 1)) }},
        owner_name: '{{ old('owner_name', '') }}',
        email: '{{ old('email', '') }}',
        owner_ktp_number: '{{ old('owner_ktp_number', '') }}',
        workshop_name: '{{ old('workshop_name', '') }}',
        phone: '{{ old('phone', '') }}',
        address: '{{ old('address', '') }}',
        city: '{{ old('city', '') }}',
        province: '{{ old('province', '') }}',
        operational_hours: '{{ old('operational_hours', '') }}',
 
        validateStep1() {
            return this.owner_name.trim() !== '' && 
                   this.email.trim() !== '' && 
                   this.owner_ktp_number.trim().length === 16;
        },
        validateStep2() {
            return this.workshop_name.trim() !== '' && 
                   this.phone.trim() !== '' && 
                   this.address.trim() !== '' && 
                   this.city.trim() !== '' && 
                   this.province.trim() !== '' && 
                   this.operational_hours.trim() !== '';
        }
    }">
        {{-- Stepper Progress --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;position:relative;padding:0 8px;">
            <!-- Line background -->
            <div style="position:absolute;top:16px;left:24px;right:24px;height:2px;background:#2E3030;z-index:0;"></div>
            <!-- Line active -->
            <div :style="'width: ' + ((step - 1) * 50) + '%'" style="position:absolute;top:16px;left:24px;height:2px;background:#ff9aa4;z-index:0;transition:width 300ms;"></div>
 
            <!-- Step 1 -->
            <div style="display:flex;flex-direction:column;align-items:center;z-index:1;cursor:pointer;" @click="step = 1">
                <div :style="step >= 1 ? 'background-color:#410008;border-color:#ff9aa4;color:#ff9aa4;' : 'background-color:#121414;border-color:#2E3030;color:#71717A;'"
                     style="width:32px;height:32px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all 300ms;">
                    1
                </div>
                <span style="font-size:11px;font-weight:600;margin-top:6px;" :style="step >= 1 ? 'color:#F4F4F5;' : 'color:#71717A;'">Pemilik</span>
            </div>
 
            <!-- Step 2 -->
            <div style="display:flex;flex-direction:column;align-items:center;z-index:1;cursor:pointer;" @click="if (validateStep1()) step = 2">
                <div :style="step >= 2 ? 'background-color:#410008;border-color:#ff9aa4;color:#ff9aa4;' : 'background-color:#121414;border-color:#2E3030;color:#71717A;'"
                     style="width:32px;height:32px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all 300ms;">
                    2
                </div>
                <span style="font-size:11px;font-weight:600;margin-top:6px;" :style="step >= 2 ? 'color:#F4F4F5;' : 'color:#71717A;'">Bengkel</span>
            </div>
 
            <!-- Step 3 -->
            <div style="display:flex;flex-direction:column;align-items:center;z-index:1;cursor:pointer;" @click="if (validateStep1() && validateStep2()) step = 3">
                <div :style="step >= 3 ? 'background-color:#410008;border-color:#ff9aa4;color:#ff9aa4;' : 'background-color:#121414;border-color:#2E3030;color:#71717A;'"
                     style="width:32px;height:32px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all 300ms;">
                    3
                </div>
                <span style="font-size:11px;font-weight:600;margin-top:6px;" :style="step >= 3 ? 'color:#F4F4F5;' : 'color:#71717A;'">Dokumen</span>
            </div>
        </div>
 
        <form method="POST" action="{{ route('register.workshop') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:18px;">
            @csrf
 
            {{-- STEP 1: Informasi Pemilik --}}
            <div x-show="step === 1" x-transition.fade class="space-y-4">
                <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
                    <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Langkah 1: Informasi Pemilik</p>
 
                    {{-- Nama Pemilik --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="owner_name" class="form-label">Nama Pemilik / Pengelola</label>
                        <input id="owner_name" type="text" name="owner_name" x-model="owner_name"
                            required autofocus placeholder="Nama lengkap pemilik"
                            class="form-input {{ $errors->has('owner_name') ? 'form-input-error' : '' }}" />
                        @error('owner_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Email Pemilik --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="email" class="form-label">Email Pemilik (Untuk Login)</label>
                        <input id="email" type="email" name="email" x-model="email"
                            required placeholder="nama@email.com"
                            class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}" />
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- NIK KTP --}}
                    <div class="form-group">
                        <label for="owner_ktp_number" class="form-label">Nomor KTP (NIK) Pemilik</label>
                        <input id="owner_ktp_number" type="text" name="owner_ktp_number" x-model="owner_ktp_number"
                            required placeholder="16 digit NIK KTP" maxlength="16"
                            class="form-input {{ $errors->has('owner_ktp_number') ? 'form-input-error' : '' }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        @error('owner_ktp_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
 
                <button type="button" @click="if (validateStep1()) { step = 2 } else { alert('Mohon lengkapi data pemilik dengan benar (KTP harus 16 digit).') }" class="btn-primary" style="width:100%;justify-content:center;margin-top:4px;">
                    Lanjutkan
                    <svg style="width:16px;height:16px;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
 
            {{-- STEP 2: Informasi Bengkel --}}
            <div x-show="step === 2" x-transition.fade class="space-y-4" style="display:none;">
                <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
                    <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Langkah 2: Informasi Bengkel</p>
 
                    {{-- Nama Bengkel --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="workshop_name" class="form-label">Nama Bengkel</label>
                        <input id="workshop_name" type="text" name="workshop_name" x-model="workshop_name"
                            required placeholder="Contoh: Bengkel Jaya Motor"
                            class="form-input {{ $errors->has('workshop_name') ? 'form-input-error' : '' }}" />
                        @error('workshop_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Nomor Telepon Bengkel --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="phone" class="form-label">Nomor Telepon Bengkel</label>
                        <input id="phone" type="tel" name="phone" x-model="phone"
                            required placeholder="Contoh: 08123456789"
                            class="form-input {{ $errors->has('phone') ? 'form-input-error' : '' }}" />
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Alamat Lengkap --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="address" class="form-label">Alamat Lengkap Bengkel</label>
                        <textarea id="address" name="address" x-model="address" required rows="2"
                            placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan"
                            class="form-textarea {{ $errors->has('address') ? 'form-input-error' : '' }}" style="min-height:70px;"></textarea>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Kota & Provinsi --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div class="form-group">
                            <label for="city" class="form-label">Kota / Kabupaten</label>
                            <input id="city" type="text" name="city" x-model="city"
                                required placeholder="Jakarta"
                                class="form-input {{ $errors->has('city') ? 'form-input-error' : '' }}" />
                            @error('city') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="province" class="form-label">Provinsi</label>
                            <input id="province" type="text" name="province" x-model="province"
                                required placeholder="DKI Jakarta"
                                class="form-input {{ $errors->has('province') ? 'form-input-error' : '' }}" />
                            @error('province') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
 
                    {{-- Jam Operasional --}}
                    <div class="form-group">
                        <label for="operational_hours" class="form-label">Jam Operasional</label>
                        <input id="operational_hours" type="text" name="operational_hours" x-model="operational_hours"
                            required placeholder="Contoh: Senin - Sabtu: 08:00 - 17:00"
                            class="form-input {{ $errors->has('operational_hours') ? 'form-input-error' : '' }}" />
                        @error('operational_hours') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
 
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;margin-top:4px;">
                    <button type="button" @click="step = 1" class="btn-primary" style="justify-content:center;background:#2E3030;border-color:#3A3D3D;">
                        Kembali
                    </button>
                    <button type="button" @click="if (validateStep2()) { step = 3 } else { alert('Mohon lengkapi semua data bengkel.') }" class="btn-primary" style="justify-content:center;">
                        Lanjutkan
                        <svg style="width:16px;height:16px;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
 
            {{-- STEP 3: Dokumen & Password --}}
            <div x-show="step === 3" x-transition.fade class="space-y-4" style="display:none;">
                <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
                    <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Langkah 3: Dokumen & Password</p>
 
                    {{-- Dokumen Legalitas --}}
                    <div class="form-group" style="margin-bottom:12px;">
                        <label for="legal_document" class="form-label">Dokumen Legalitas (NIB/SIUP/KTP Pemilik)</label>
                        <input id="legal_document" type="file" name="legal_document"
                            required accept=".pdf,.jpg,.jpeg,.png"
                            class="form-input {{ $errors->has('legal_document') ? 'form-input-error' : '' }}" style="padding-top:8px;" />
                        <p style="font-size:11px;color:#71717A;margin-top:4px;">Format: PDF, JPG, JPEG, PNG (Maks. 10MB)</p>
                        @error('legal_document') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Kata Sandi --}}
                    <div class="form-group" style="margin-bottom:12px;" x-data="{ show: false }">
                        <label for="password" class="form-label">Password Akun</label>
                        <div style="position:relative;margin-top:4px;">
                            <input id="password" :type="show ? 'text' : 'password'" name="password"
                                required autocomplete="new-password" placeholder="Min. 8 karakter"
                                class="form-input {{ $errors->has('password') ? 'form-input-error' : '' }}" style="padding-right:42px;" />
                            <button type="button" @click="show = !show"
                                    style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#71717A;">
                                <svg x-show="!show" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" style="width:16px;height:16px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Konfirmasi Kata Sandi --}}
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Akun</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required autocomplete="new-password" placeholder="Ulangi password"
                            class="form-input {{ $errors->has('password_confirmation') ? 'form-input-error' : '' }}" />
                        @error('password_confirmation') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
 
                {{-- Info Approval --}}
                <div style="background-color:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:10px;padding:12px 14px;display:flex;gap:10px;align-items:flex-start;">
                    <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size:12px;color:#fbbf24;line-height:1.5;margin:0;">
                        Pendaftaran bengkel memerlukan verifikasi dari admin Maintify. Proses verifikasi membutuhkan 1-2 hari kerja.
                    </p>
                </div>
 
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px;margin-top:4px;">
                    <button type="button" @click="step = 2" class="btn-primary" style="justify-content:center;background:#2E3030;border-color:#3A3D3D;">
                        Kembali
                    </button>
                    <button type="submit" id="btn-register-workshop" class="btn-primary" style="justify-content:center;">
                        <svg style="width:16px;height:16px;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Daftar Sebagai Bengkel Mitra
                    </button>
                </div>
            </div>
 
            {{-- Login Link --}}
            <p style="text-align:center;font-size:13px;color:#71717A;margin-top:12px;">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                   style="color:#ff9aa4;font-weight:600;margin-left:4px;transition:color 150ms;"
                   onmouseover="this.style.color='#ff5f71'"
                   onmouseout="this.style.color='#ff9aa4'">
                    Masuk di sini
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>
