<x-guest-layout>
    @section('title', 'Daftar Akun')

    <!-- Heading -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
        <p class="text-gray-500 text-sm mt-1">Daftarkan diri Anda ke platform Maintify</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="label">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap Anda"
                class="input {{ $errors->has('name') ? 'input-error' : '' }}"
            />
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="nama@email.com"
                class="input {{ $errors->has('email') ? 'input-error' : '' }}"
            />
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="label">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Min. 8 karakter"
                class="input {{ $errors->has('password') ? 'input-error' : '' }}"
            />
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="label">Konfirmasi Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi password Anda"
                class="input {{ $errors->has('password_confirmation') ? 'input-error' : '' }}"
            />
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary w-full mt-2" id="btn-register">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
            Buat Akun
        </button>

        <!-- Login Link -->
        <p class="text-center text-sm text-gray-500 mt-4">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-[#410008] font-semibold hover:text-[#6D0013] transition-colors">
                Masuk di sini
            </a>
        </p>
    </form>
</x-guest-layout>
