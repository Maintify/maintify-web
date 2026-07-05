<x-guest-layout>
    @section('title', 'Reset Password')

    {{-- Heading --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:700;color:#F4F4F5;letter-spacing:-0.02em;">Reset Password 🔒</h2>
        <p style="color:#71717A;font-size:14px;margin-top:4px;">
            Masukkan email Anda dan password baru di bawah ini.
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

    {{-- Error Banner --}}
    @if ($errors->any())
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg style="width:16px;height:16px;color:#f87171;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="color:#f87171;font-size:13px;">{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" style="display:flex;flex-direction:column;gap:18px;">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label" style="font-size:13px;font-weight:600;letter-spacing:0.01em;">Email</label>
            <div style="position:relative;margin-top:6px;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input id="email" class="form-input {{ $errors->has('email') ? 'form-input-error' : '' }}" style="padding-left:42px;" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
            </div>
            @error('email')
                <p class="form-error" style="margin-top:6px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group" x-data="{ show: false }">
            <label for="password" class="form-label" style="font-size:13px;font-weight:600;letter-spacing:0.01em;">Password Baru</label>
            <div style="position:relative;margin-top:6px;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input id="password" class="form-input {{ $errors->has('password') ? 'form-input-error' : '' }}" style="padding-left:42px;padding-right:42px;" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="••••••••" />
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
            @error('password')
                <p class="form-error" style="margin-top:6px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group" x-data="{ show: false }">
            <label for="password_confirmation" class="form-label" style="font-size:13px;font-weight:600;letter-spacing:0.01em;">Konfirmasi Password Baru</label>
            <div style="position:relative;margin-top:6px;">
                <svg style="width:16px;height:16px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#71717A;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input id="password_confirmation" class="form-input" style="padding-left:42px;padding-right:42px;" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
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
            @error('password_confirmation')
                <p class="form-error" style="margin-top:6px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:14px;letter-spacing:0.01em;border-radius:12px;margin-top:8px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Reset Password
        </button>
    </form>
</x-guest-layout>
