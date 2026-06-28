<x-guest-layout>
    @section('title', 'Masuk')

    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Selamat Datang 👋</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">Masuk ke akun Maintify Anda</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div style="background-color:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);border-radius:10px;padding:12px 14px;margin-bottom:16px;color:#4ade80;font-size:13px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
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
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="form-input pr-10 {{ $errors->has('password') ? 'form-input-error' : '' }}"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                    style="color:#71717A;"
                    @click="showPassword = !showPassword"
                    @mouseenter="$el.style.color='#A1A1AA'"
                    @mouseleave="$el.style.color='#71717A'"
                    aria-label="Toggle password visibility">
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

        {{-- Remember Me + Forgot Password --}}
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="form-checkbox"
                />
                <span style="font-size:13px;color:#A1A1AA;">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   style="font-size:13px;color:#ff9aa4;font-weight:500;transition:color 150ms;"
                   onmouseover="this.style.color='#ff5f71'"
                   onmouseout="this.style.color='#ff9aa4'">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit Button --}}
        <button type="submit" id="btn-login" class="btn-primary w-full" style="width:100%;margin-top:8px;justify-content:center;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk
        </button>

        {{-- Divider --}}
        <div class="divider-text">atau</div>

        {{-- Register Link --}}
        @if (Route::has('register'))
            <p style="text-align:center;font-size:13px;color:#71717A;">
                Belum punya akun?
                <a href="{{ route('register') }}"
                   style="color:#ff9aa4;font-weight:600;margin-left:4px;transition:color 150ms;"
                   onmouseover="this.style.color='#ff5f71'"
                   onmouseout="this.style.color='#ff9aa4'">
                    Daftar sekarang
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
