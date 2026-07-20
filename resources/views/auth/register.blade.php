<x-guest-layout>
    @section('title', 'Daftar Akun')

    {{-- ── Header ── --}}
    <div style="margin-bottom:28px;">
        <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:100px;background:rgba(65,0,8,0.2);border:1px solid rgba(255,154,164,0.2);margin-bottom:14px;">
            <span style="font-size:11px;font-weight:600;color:#ff9aa4;letter-spacing:0.04em;text-transform:uppercase;">Maintify · Bergabung Gratis</span>
        </div>
        <h2 style="font-size:24px;font-weight:800;color:#F4F4F5;letter-spacing:-0.03em;line-height:1.2;margin-bottom:6px;">Buat Akun Baru 🚀</h2>
        <p style="color:#71717A;font-size:13px;line-height:1.6;">Daftarkan diri dan mulai kelola histori service kendaraan Anda secara digital</p>
    </div>

    <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:16px;" 
          x-data="{ selected: '{{ old('role', 'vehicle_owner') }}' }"
          @submit="if (selected === 'workshop') { $event.preventDefault(); window.location.href = '{{ route('register.workshop') }}'; }">
        @csrf

        {{-- Role Selection — FIRST so user understands context --}}
        <div>
            <label style="font-size:13px;font-weight:600;color:#F4F4F5;letter-spacing:0.01em;display:block;margin-bottom:10px;">
                Saya mendaftar sebagai
            </label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">

                {{-- Vehicle Owner --}}
                <label for="role_owner"
                       @click="selected = 'vehicle_owner'"
                       :style="selected === 'vehicle_owner'
                           ? 'border-color:#410008;background:rgba(65,0,8,0.15);color:#ff9aa4;'
                           : 'border-color:#2E3030;background:#1E2020;color:#71717A;'"
                       style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:16px 10px;border:1.5px solid;border-radius:14px;cursor:pointer;transition:all 150ms;text-align:center;">
                    <input type="radio" id="role_owner" name="role" value="vehicle_owner" class="sr-only" :checked="selected === 'vehicle_owner'">
                    <div :style="selected === 'vehicle_owner' ? 'background:rgba(65,0,8,0.3);color:#ff9aa4;' : 'background:#2A2D2D;color:#71717A;'"
                         style="width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;transition:all 150ms;">
                        <svg style="width:22px;height:22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;margin:0 0 2px;">Pemilik Kendaraan</p>
                        <p style="font-size:11px;color:#71717A;margin:0;line-height:1.4;">Pantau & kelola kendaraan</p>
                    </div>
                    <div x-show="selected === 'vehicle_owner'"
                         style="width:18px;height:18px;border-radius:50%;background:#410008;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:10px;height:10px;color:white;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </label>

                {{-- Workshop --}}
                <label for="role_workshop"
                       @click="selected = 'workshop'"
                       :style="selected === 'workshop'
                           ? 'border-color:#410008;background:rgba(65,0,8,0.15);color:#ff9aa4;'
                           : 'border-color:#2E3030;background:#1E2020;color:#71717A;'"
                       style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:16px 10px;border:1.5px solid;border-radius:14px;cursor:pointer;transition:all 150ms;text-align:center;">
                    <input type="radio" id="role_workshop" name="role" value="workshop" class="sr-only" :checked="selected === 'workshop'">
                    <div :style="selected === 'workshop' ? 'background:rgba(65,0,8,0.3);color:#ff9aa4;' : 'background:#2A2D2D;color:#71717A;'"
                         style="width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;transition:all 150ms;">
                        <svg style="width:22px;height:22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;margin:0 0 2px;">Bengkel Mitra</p>
                        <p style="font-size:11px;color:#71717A;margin:0;line-height:1.4;">Catat & kelola service</p>
                    </div>
                    <div x-show="selected === 'workshop'"
                         style="width:18px;height:18px;border-radius:50%;background:#410008;display:flex;align-items:center;justify-content:center;display:none;">
                        <svg style="width:10px;height:10px;color:white;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </label>
            </div>
            @error('role')
                <p class="form-error" style="margin-top:6px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Divider --}}
        <div x-show="selected === 'vehicle_owner'" style="display:flex;align-items:center;gap:12px;">
            <div style="flex:1;height:1px;background:#2E3030;"></div>
            <span style="font-size:11px;color:#3A3D3D;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;">Data Akun</span>
            <div style="flex:1;height:1px;background:#2E3030;"></div>
        </div>

        {{-- Name --}}
        <div x-show="selected === 'vehicle_owner'" class="form-group">
            <label for="name" style="font-size:13px;font-weight:600;color:#F4F4F5;letter-spacing:0.01em;">Nama Lengkap</label>
            <div style="position:relative;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    :required="selected === 'vehicle_owner'"
                    :disabled="selected === 'workshop'"
                    autofocus
                    autocomplete="name"
                    placeholder="Nama lengkap Anda"
                    style="padding-left:42px;"
                    class="form-input {{ $errors->has('name') ? 'form-input-error' : '' }}"
                />
            </div>
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div x-show="selected === 'vehicle_owner'" class="form-group">
            <label for="email" style="font-size:13px;font-weight:600;color:#F4F4F5;letter-spacing:0.01em;">Alamat Email</label>
            <div style="position:relative;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    :required="selected === 'vehicle_owner'"
                    :disabled="selected === 'workshop'"
                    autocomplete="username"
                    placeholder="nama@email.com"
                    style="padding-left:42px;"
                    class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}"
                />
            </div>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password + Confirm in 2 cols on desktop --}}
        <div x-show="selected === 'vehicle_owner'" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

            {{-- Password --}}
            <div class="form-group" x-data="{ show: false }">
                <label for="password" style="font-size:13px;font-weight:600;color:#F4F4F5;letter-spacing:0.01em;">Password</label>
                <div style="position:relative;">
                    <svg style="width:15px;height:15px;position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input
                        id="password"
                        :type="show ? 'text' : 'password'"
                        name="password"
                        :required="selected === 'vehicle_owner'"
                        :disabled="selected === 'workshop'"
                        autocomplete="new-password"
                        placeholder="Min. 8 karakter"
                        style="padding-left:36px;padding-right:36px;font-size:13px;"
                        class="form-input {{ $errors->has('password') ? 'form-input-error' : '' }}"
                    />
                    <button type="button" @click="show = !show"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#71717A;">
                        <svg x-show="!show" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" style="width:14px;height:14px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="form-error" style="font-size:11px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="form-group" x-data="{ show: false }">
                <label for="password_confirmation" style="font-size:13px;font-weight:600;color:#F4F4F5;letter-spacing:0.01em;">Konfirmasi</label>
                <div style="position:relative;">
                    <svg style="width:15px;height:15px;position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <input
                        id="password_confirmation"
                        :type="show ? 'text' : 'password'"
                        name="password_confirmation"
                        :required="selected === 'vehicle_owner'"
                        :disabled="selected === 'workshop'"
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        style="padding-left:36px;padding-right:36px;font-size:13px;"
                        class="form-input {{ $errors->has('password_confirmation') ? 'form-input-error' : '' }}"
                    />
                    <button type="button" @click="show = !show"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#71717A;">
                        <svg x-show="!show" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" style="width:14px;height:14px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="form-error" style="font-size:11px;">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Terms note --}}
        <p style="font-size:12px;color:#71717A;line-height:1.5;">
            Dengan mendaftar, Anda menyetujui
            <a href="#" style="color:#ff9aa4;text-decoration:none;">Syarat &amp; Ketentuan</a>
            dan <a href="#" style="color:#ff9aa4;text-decoration:none;">Kebijakan Privasi</a> Maintify.
        </p>

        {{-- Submit --}}
        <button x-show="selected === 'vehicle_owner'" type="submit"
                id="btn-register"
                class="btn-primary"
                style="width:100%;justify-content:center;padding:13px;font-size:14px;letter-spacing:0.01em;border-radius:12px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Buat Akun Sekarang
        </button>

        <button x-show="selected === 'workshop'" type="button"
                @click="window.location.href = '{{ route('register.workshop') }}'"
                id="btn-register-workshop"
                class="btn-primary"
                style="display:none; width:100%;justify-content:center;padding:13px;font-size:14px;letter-spacing:0.01em;border-radius:12px;">
            Lanjutkan Pendaftaran Bengkel
            <svg style="width:16px;height:16px;margin-left:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Login link --}}
        <p style="text-align:center;font-size:13px;color:#71717A;margin-top:4px;">
            Sudah punya akun?
            <a href="{{ route('login') }}"
               style="color:#ff9aa4;font-weight:600;margin-left:4px;text-decoration:none;transition:color 150ms;"
               onmouseover="this.style.color='#ff5f71'"
               onmouseout="this.style.color='#ff9aa4'">
                Masuk di sini →
            </a>
        </p>
    </form>
</x-guest-layout>
