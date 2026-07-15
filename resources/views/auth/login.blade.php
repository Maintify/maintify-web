<x-guest-layout>
    @section('title', 'Masuk')

    {{-- ── Brand tagline ── --}}
    <div style="margin-bottom:32px;">
        <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:100px;background:rgba(65,0,8,0.2);border:1px solid rgba(255,154,164,0.2);margin-bottom:16px;">
            <span style="width:6px;height:6px;border-radius:50%;background:#ff9aa4;display:inline-block;animation:pulse 2s infinite;"></span>
            <span style="font-size:11px;font-weight:600;color:#ff9aa4;letter-spacing:0.04em;text-transform:uppercase;">Real-Time · Digital ID</span>
        </div>
        <h2 style="font-size:26px;font-weight:800;color:#F4F4F5;letter-spacing:-0.03em;line-height:1.2;margin-bottom:6px;">Selamat Datang<br>Kembali</h2>
        <p style="color:#71717A;font-size:14px;line-height:1.6;">Masuk untuk mengelola histori service kendaraan Anda</p>
    </div>

    {{-- ── Session Status ── --}}
    @if (session('status'))
        <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg style="width:16px;height:16px;color:#4ade80;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="color:#4ade80;font-size:13px;">{{ session('status') }}</span>
        </div>
    @endif

    {{-- ── Error Banner ── --}}
    @if ($errors->any())
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg style="width:16px;height:16px;color:#f87171;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="color:#f87171;font-size:13px;">{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:18px;">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label" style="font-size:13px;font-weight:600;letter-spacing:0.01em;">
                Alamat Email
            </label>
            <div style="position:relative;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                    style="padding-left:42px;"
                    class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}"
                />
            </div>
        </div>

        {{-- Password --}}
        <div class="form-group" x-data="{ show: false }">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <label for="password" style="font-size:13px;font-weight:600;color:#F4F4F5;letter-spacing:0.01em;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       style="font-size:12px;color:#ff9aa4;font-weight:500;text-decoration:none;transition:color 150ms;"
                       onmouseover="this.style.color='#ff5f71'"
                       onmouseout="this.style.color='#ff9aa4'">
                        Lupa password?
                    </a>
                @endif
            </div>
            <div style="position:relative;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    style="padding-left:42px;padding-right:42px;"
                    class="form-input {{ $errors->has('password') ? 'form-input-error' : '' }}"
                />
                <button type="button"
                        @click="show = !show"
                        style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#71717A;transition:color 150ms;"
                        onmouseover="this.style.color='#A1A1AA'"
                        onmouseout="this.style.color='#71717A'"
                        aria-label="Toggle password visibility">
                    <svg x-show="!show" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" style="width:16px;height:16px;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember Me --}}
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none;"
               x-data="{ checked: {{ old('remember') ? 'true' : 'false' }} }">
            <div style="position:relative;width:18px;height:18px;flex-shrink:0;">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    @if(old('remember')) checked @endif
                    x-model="checked"
                    style="opacity:0;position:absolute;width:100%;height:100%;cursor:pointer;margin:0;"
                />
                <div :style="checked ? 'background-color:#410008;border-color:#410008;' : 'background-color:transparent;border-color:#52565A;'"
                     style="width:18px;height:18px;border-radius:5px;border:1.5px solid;transition:background-color 150ms,border-color 150ms;display:flex;align-items:center;justify-content:center;">
                    <svg x-show="checked" x-cloak style="width:12px;height:12px;color:#ff9aa4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <span style="font-size:13px;color:#A1A1AA;">Ingat saya di perangkat ini</span>
        </label>

        {{-- Submit --}}
        <button type="submit"
                id="btn-login"
                class="btn-primary"
                style="width:100%;justify-content:center;padding:13px;font-size:14px;letter-spacing:0.01em;border-radius:12px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
            </svg>
            Masuk ke Akun
        </button>

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;color:#3A3D3D;">
            <div style="flex:1;height:1px;background:#2E3030;"></div>
            <span style="font-size:12px;color:#71717A;white-space:nowrap;">atau</span>
            <div style="flex:1;height:1px;background:#2E3030;"></div>
        </div>

        {{-- Register CTA --}}
        @if (Route::has('register'))
            <div style="text-align:center;padding:16px;background:rgba(255,255,255,0.02);border:1px solid #2E3030;border-radius:12px;">
                <p style="font-size:13px;color:#71717A;margin:0 0 10px;">Belum punya akun Maintify?</p>
                <a href="{{ route('register') }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:9px 20px;border-radius:10px;border:1px solid #2E3030;background:transparent;color:#F4F4F5;font-size:13px;font-weight:600;text-decoration:none;transition:all 150ms;"
                   onmouseover="this.style.borderColor='#410008';this.style.backgroundColor='rgba(65,0,8,0.1)';this.style.color='#ff9aa4';"
                   onmouseout="this.style.borderColor='#2E3030';this.style.backgroundColor='transparent';this.style.color='#F4F4F5';">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Daftar Gratis Sekarang
                </a>
            </div>
        @endif
    </form>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</x-guest-layout>
