<x-guest-layout>
    @section('title', 'Daftar Akun')

    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Buat Akun Baru</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">Daftarkan diri Anda ke platform Maintify</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap Anda"
                class="form-input {{ $errors->has('name') ? 'form-input-error' : '' }}"
            />
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="nama@email.com"
                class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}"
            />
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group" x-data="{ showPassword: false }">
            <label for="password" class="form-label">Password</label>
            <div class="relative">
                <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Min. 8 karakter"
                    class="form-input pr-10 {{ $errors->has('password') ? 'form-input-error' : '' }}"
                />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                        style="color:#71717A;"
                        @click="showPassword = !showPassword"
                        aria-label="Toggle password">
                    <svg x-show="!showPassword" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPassword" style="width:16px;height:16px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group" x-data="{ showConfirm: false }">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password Anda"
                    class="form-input pr-10 {{ $errors->has('password_confirmation') ? 'form-input-error' : '' }}"
                />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                        style="color:#71717A;"
                        @click="showConfirm = !showConfirm"
                        aria-label="Toggle password">
                    <svg x-show="!showConfirm" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showConfirm" style="width:16px;height:16px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role Selection --}}
        <div class="form-group" x-data="{ selected: '{{ old('role', 'vehicle_owner') }}' }">
            <label class="form-label">Daftar Sebagai</label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:4px;">

                <label for="role_owner"
                       class="role-option"
                       :class="{ 'active': selected === 'vehicle_owner' }"
                       @click="selected = 'vehicle_owner'">
                    <input type="radio" id="role_owner" name="role" value="vehicle_owner"
                           class="sr-only" :checked="selected === 'vehicle_owner'">
                    <svg style="width:24px;height:24px;margin-bottom:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span style="font-size:13px;font-weight:500;">Pemilik Kendaraan</span>
                </label>

                <label for="role_workshop"
                       class="role-option"
                       :class="{ 'active': selected === 'workshop' }"
                       @click="selected = 'workshop'">
                    <input type="radio" id="role_workshop" name="role" value="workshop"
                           class="sr-only" :checked="selected === 'workshop'">
                    <svg style="width:24px;height:24px;margin-bottom:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span style="font-size:13px;font-weight:500;">Bengkel Mitra</span>
                </label>
            </div>
            @error('role')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" id="btn-register" class="btn-primary" style="width:100%;margin-top:8px;justify-content:center;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Buat Akun
        </button>

        {{-- Login Link --}}
        <p style="text-align:center;font-size:13px;color:#71717A;margin-top:16px;">
            Sudah punya akun?
            <a href="{{ route('login') }}"
               style="color:#ff9aa4;font-weight:600;margin-left:4px;transition:color 150ms;"
               onmouseover="this.style.color='#ff5f71'"
               onmouseout="this.style.color='#ff9aa4'">
                Masuk di sini
            </a>
        </p>
    </form>

    <style>
        .role-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 14px 10px;
            border: 1.5px solid #2E3030;
            border-radius: 12px;
            cursor: pointer;
            transition: all 150ms ease;
            color: #71717A;
            background-color: #1E2020;
        }
        .role-option:hover {
            border-color: #410008;
            color: #F4F4F5;
            background-color: rgba(65,0,8,0.1);
        }
        .role-option.active {
            border-color: #410008;
            background-color: rgba(65,0,8,0.2);
            color: #ff9aa4;
        }
    </style>
</x-guest-layout>
