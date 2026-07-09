<x-app-layout>
    @slot('pageTitle', 'Ubah Staf')
    @slot('breadcrumb', 'Workshop / Staf / Ubah')

    <div class="max-w-xl mx-auto px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('workshop.staff.index') }}"
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 hover:border-zinc-500 transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Ubah Staf</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Ubah rincian akun pegawai atau mekanik bengkel Anda</p>
            </div>
        </div>

        {{-- ── Form Card ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-xl">
            <form method="POST" action="{{ route('workshop.staff.update', $staff) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Full Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $staff->user->name) }}"
                           required
                           placeholder="Contoh: Budi Santoso"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $staff->user->email) }}"
                           required
                           placeholder="Contoh: budi@gmail.com"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone Number --}}
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="phone_number"
                           name="phone_number"
                           value="{{ old('phone_number', $staff->user->phone_number) }}"
                           required
                           placeholder="Contoh: 081234567890"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('phone_number')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Position --}}
                <div>
                    <label for="position" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Posisi Staf <span class="text-red-500">*</span>
                    </label>
                    <select id="position"
                            name="position"
                            required
                            class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                        <option value="mechanic" {{ old('position', $staff->position) === 'mechanic' ? 'selected' : '' }}>Mekanik</option>
                        <option value="admin" {{ old('position', $staff->position) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('position')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="pt-2 border-t border-zinc-800">
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Kata Sandi Baru <span class="text-zinc-500 text-xs font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="Minimal 8 karakter"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Ulangi kata sandi baru"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('password_confirmation')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active Toggle --}}
                <div class="flex items-center gap-3 py-2 border-t border-zinc-800">
                    <div class="flex items-center h-5">
                        <input id="is_active"
                               name="is_active"
                               type="checkbox"
                               value="1"
                               {{ old('is_active', $staff->is_active) ? 'checked' : '' }}
                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-zinc-700 bg-zinc-950 rounded">
                    </div>
                    <div class="text-sm">
                        <label for="is_active" class="font-medium text-zinc-300">Aktif</label>
                        <p class="text-zinc-500 text-xs">Jika dinonaktifkan, staf tidak akan bisa login ke aplikasi.</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-zinc-800">
                    <button type="submit"
                            class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-red-900/30 transition-all text-sm">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('workshop.staff.index') }}"
                       class="px-5 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 font-medium rounded-xl transition-all text-sm">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>
