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

        <!-- Role -->
        <div>
            <label class="label">Daftar Sebagai</label>
            <div class="grid grid-cols-2 gap-3 mt-1">
                <label class="role-card {{ old('role', 'vehicle_owner') === 'vehicle_owner' ? 'role-card-active' : '' }}" for="role_owner">
                    <input type="radio" id="role_owner" name="role" value="vehicle_owner" class="sr-only" {{ old('role', 'vehicle_owner') === 'vehicle_owner' ? 'checked' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    </svg>
                    <span class="text-sm font-medium">Pemilik Kendaraan</span>
                </label>
                <label class="role-card {{ old('role') === 'workshop' ? 'role-card-active' : '' }}" for="role_workshop">
                    <input type="radio" id="role_workshop" name="role" value="workshop" class="sr-only" {{ old('role') === 'workshop' ? 'checked' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                    <span class="text-sm font-medium">Bengkel Mitra</span>
                </label>
            </div>
            @error('role')
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

    <style>
        .role-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            color: #6B7280;
        }
        .role-card:hover {
            border-color: #410008;
            color: #410008;
        }
        .role-card-active {
            border-color: #410008;
            background-color: #F5E8EB;
            color: #410008;
        }
    </style>

    <script>
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.role-card').forEach(card => {
                    card.classList.remove('role-card-active');
                });
                this.closest('.role-card').classList.add('role-card-active');
            });
        });
    </script>
</x-guest-layout>
