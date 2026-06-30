<x-guest-layout>
    @section('title', 'Daftar Bengkel Mitra')

    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Daftar Bengkel Mitra 🔧</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">Daftarkan bengkel Anda ke platform Maintify</p>
    </div>

    <form method="POST" action="{{ route('register.workshop') }}" class="space-y-4">
        @csrf

        {{-- Section: Akun --}}
        <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;margin-bottom:8px;">
            <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Informasi Akun</p>

            {{-- Nama --}}
            <div class="form-group" style="margin-bottom:12px;">
                <label for="name" class="form-label">Nama Pengelola</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                    required autofocus autocomplete="name" placeholder="Nama lengkap Anda"
                    class="form-input {{ $errors->has('name') ? 'form-input-error' : '' }}" />
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div class="form-group" style="margin-bottom:12px;">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    required autocomplete="username" placeholder="nama@email.com"
                    class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}" />
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="form-group" style="margin-bottom:12px;" x-data="{ show: false }">
                <label for="password" class="form-label">Password</label>
                <div class="relative">
                    <input id="password" :type="show ? 'text' : 'password'" name="password"
                        required autocomplete="new-password" placeholder="Min. 8 karakter"
                        class="form-input pr-10 {{ $errors->has('password') ? 'form-input-error' : '' }}" />
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2" style="color:#71717A;">
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

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    required autocomplete="new-password" placeholder="Ulangi password"
                    class="form-input {{ $errors->has('password_confirmation') ? 'form-input-error' : '' }}" />
                @error('password_confirmation') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section: Info Bengkel --}}
        <div style="background-color:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;padding:16px;">
            <p style="font-size:11px;font-weight:600;color:#71717A;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Informasi Bengkel</p>

            {{-- Nama Bengkel --}}
            <div class="form-group" style="margin-bottom:12px;">
                <label for="workshop_name" class="form-label">Nama Bengkel</label>
                <input id="workshop_name" type="text" name="workshop_name" value="{{ old('workshop_name') }}"
                    required placeholder="Contoh: Bengkel Jaya Motor"
                    class="form-input {{ $errors->has('workshop_name') ? 'form-input-error' : '' }}" />
                @error('workshop_name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div class="form-group" style="margin-bottom:12px;">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                    required placeholder="Contoh: 08123456789"
                    class="form-input {{ $errors->has('phone') ? 'form-input-error' : '' }}" />
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Alamat --}}
            <div class="form-group" style="margin-bottom:12px;">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <textarea id="address" name="address" required rows="2"
                    placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan"
                    class="form-textarea {{ $errors->has('address') ? 'form-input-error' : '' }}" style="min-height:70px;">{{ old('address') }}</textarea>
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Kota & Provinsi --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label for="city" class="form-label">Kota</label>
                    <input id="city" type="text" name="city" value="{{ old('city') }}"
                        required placeholder="Jakarta"
                        class="form-input {{ $errors->has('city') ? 'form-input-error' : '' }}" />
                    @error('city') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="province" class="form-label">Provinsi</label>
                    <input id="province" type="text" name="province" value="{{ old('province') }}"
                        required placeholder="DKI Jakarta"
                        class="form-input {{ $errors->has('province') ? 'form-input-error' : '' }}" />
                    @error('province') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Info Approval --}}
        <div style="background-color:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:10px;padding:12px 14px;display:flex;gap:10px;align-items:flex-start;">
            <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:12px;color:#fbbf24;line-height:1.5;">
                Pendaftaran bengkel memerlukan verifikasi dari admin Maintify. Proses verifikasi membutuhkan 1-2 hari kerja.
            </p>
        </div>

        {{-- Submit --}}
        <button type="submit" id="btn-register-workshop" class="btn-primary" style="width:100%;justify-content:center;margin-top:4px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Daftar Sebagai Bengkel Mitra
        </button>

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
</x-guest-layout>
