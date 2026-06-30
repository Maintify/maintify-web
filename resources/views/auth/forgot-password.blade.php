<x-guest-layout>
    @section('title', 'Lupa Password')

    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Lupa Password? 🔑</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">
            Masukkan email Anda dan kami akan mengirimkan link reset password.
        </p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div style="background-color:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);border-radius:10px;padding:12px 14px;margin-bottom:16px;color:#4ade80;font-size:13px;display:flex;align-items:center;gap:8px;">
            <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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
                autocomplete="email"
                placeholder="nama@email.com"
                class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}"
            />
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" id="btn-reset-password" class="btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Kirim Link Reset Password
        </button>

        {{-- Back to Login --}}
        <p style="text-align:center;font-size:13px;color:#71717A;margin-top:16px;">
            <a href="{{ route('login') }}"
               style="color:#ff9aa4;font-weight:500;display:inline-flex;align-items:center;gap:4px;transition:color 150ms;"
               onmouseover="this.style.color='#ff5f71'"
               onmouseout="this.style.color='#ff9aa4'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18l-6-6 6-6"/>
                </svg>
                Kembali ke halaman masuk
            </a>
        </p>
    </form>
</x-guest-layout>
