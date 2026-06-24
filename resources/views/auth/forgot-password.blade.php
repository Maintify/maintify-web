<x-guest-layout>
    @section('title', 'Lupa Password')

    <!-- Heading -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Lupa Password?</h2>
        <p class="text-gray-500 text-sm mt-1">
            Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="nama@email.com"
                class="input {{ $errors->has('email') ? 'input-error' : '' }}"
            />
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary w-full mt-2" id="btn-reset-password">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Kirim Link Reset Password
        </button>

        <!-- Back to Login -->
        <p class="text-center text-sm text-gray-500 mt-4">
            <a href="{{ route('login') }}" class="text-[#410008] font-semibold hover:text-[#6D0013] transition-colors flex items-center justify-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali ke halaman masuk
            </a>
        </p>
    </form>
</x-guest-layout>
