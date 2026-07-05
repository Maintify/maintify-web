<x-guest-layout>
    @section('title', 'Verifikasi OTP')

    {{-- Brand tagline --}}
    <div style="margin-bottom:32px;">
        <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:100px;background:rgba(65,0,8,0.2);border:1px solid rgba(255,154,164,0.2);margin-bottom:16px;">
            <span style="width:6px;height:6px;border-radius:50%;background:#ff9aa4;display:inline-block;animation:pulse 2s infinite;"></span>
            <span style="font-size:11px;font-weight:600;color:#ff9aa4;letter-spacing:0.04em;text-transform:uppercase;">Keamanan Akun</span>
        </div>
        <h2 style="font-size:26px;font-weight:800;color:#F4F4F5;letter-spacing:-0.03em;line-height:1.2;margin-bottom:6px;">Verifikasi Kedua 👋</h2>
        <p style="color:#71717A;font-size:14px;line-height:1.6;">Masukkan kode OTP 6-digit yang dikirimkan ke alamat email Anda.</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg style="width:16px;height:16px;color:#4ade80;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="color:#4ade80;font-size:13px;">{{ session('status') }}</span>
        </div>
    @endif

    {{-- Error Banner --}}
    @if ($errors->any())
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg style="width:16px;height:16px;color:#f87171;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="color:#f87171;font-size:13px;">{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- OTP Submission Form --}}
    <form method="POST" action="{{ url('otp-verify') }}" style="display:flex;flex-direction:column;gap:18px;">
        @csrf

        {{-- OTP Input --}}
        <div class="form-group">
            <label for="otp" class="form-label" style="font-size:13px;font-weight:600;letter-spacing:0.01em;">
                Kode Verifikasi OTP
            </label>
            <div style="position:relative;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input
                    id="otp"
                    type="text"
                    name="otp"
                    required
                    autofocus
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    placeholder="Contoh: 123456"
                    style="padding-left:42px;letter-spacing:0.1em;"
                    class="form-input {{ $errors->has('otp') ? 'form-input-error' : '' }}"
                />
            </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
                id="btn-verify-otp"
                class="btn-primary"
                style="width:100%;justify-content:center;padding:13px;font-size:14px;letter-spacing:0.01em;border-radius:12px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-6.837 3.82c-.118.038-.237.077-.355.118v6.2c0 4.757 3.513 8.878 8 9.2 4.487-.322 8-4.443 8-9.2v-6.2c-.118-.041-.237-.08-.355-.118z"/>
            </svg>
            Verifikasi OTP
        </button>
    </form>

    {{-- Resend OTP Section --}}
    <div style="display:flex;flex-direction:column;align-items:center;gap:12px;margin-top:20px;padding-top:16px;border-top:1px solid #2E3030;">
        <form method="POST" action="{{ route('auth.otp.resend') }}" style="width:100%;text-align:center;">
            @csrf
            <span style="font-size:13px;color:#71717A;">Tidak menerima kode? </span>
            <button type="submit"
                    style="background:none;border:none;color:#ff9aa4;font-size:13px;font-weight:600;cursor:pointer;padding:0;text-decoration:none;transition:color 150ms;"
                    onmouseover="this.style.color='#ff5f71'"
                    onmouseout="this.style.color='#ff9aa4'">
                Kirim ulang OTP
            </button>
        </form>

        <a href="{{ route('login') }}"
           style="font-size:13px;color:#71717A;text-decoration:none;transition:color 150ms;"
           onmouseover="this.style.color='#A1A1AA'"
           onmouseout="this.style.color='#71717A'">
            Kembali ke Login
        </a>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</x-guest-layout>
