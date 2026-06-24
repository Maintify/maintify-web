<x-guest-layout>
    @section('title', 'Masuk')

    <!-- Heading -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang 👋</h2>
        <p class="text-gray-500 text-sm mt-1">Masuk ke akun Maintify Anda</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="input pr-10 {{ $errors->has('password') ? 'input-error' : '' }}"
                    x-data
                    x-ref="passwordInput"
                />
                <!-- Toggle show/hide password -->
                <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                    x-data="{ show: false }"
                    @click="show = !show; $refs.passwordInput.type = show ? 'text' : 'password'"
                    aria-label="Toggle password visibility"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-gray-300 text-[#410008] focus:ring-[#410008]"
                />
                <span class="text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-[#410008] hover:text-[#6D0013] font-medium transition-colors"
                >
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary w-full mt-2" id="btn-login">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            Masuk
        </button>

        <!-- Register Link -->
        @if (Route::has('register'))
            <p class="text-center text-sm text-gray-500 mt-4">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-[#410008] font-semibold hover:text-[#6D0013] transition-colors">
                    Daftar sekarang
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
