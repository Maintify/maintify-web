<x-app-layout>
    @slot('pageTitle', 'Profil Bengkel')
    @slot('breadcrumb', 'Workshop / Profil')

    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- ── Alert Success ── --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-950/30 border border-emerald-900/40 text-emerald-400 rounded-xl text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ── Header ── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('dashboard') }}"
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 hover:border-zinc-500 transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Profil Bengkel</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Kelola informasi publik dan data operasional bengkel Anda</p>
            </div>
        </div>

        {{-- ── Form Card ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-xl">
            <form method="POST" action="{{ route('workshop.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- ── Section 1: Informasi Dasar ── --}}
                <div>
                    <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Informasi Utama</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                Nama Bengkel <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $workshop->name) }}"
                                   required
                                   class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                            @error('name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Operational Hours --}}
                        <div>
                            <label for="operational_hours" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                Jam Operasional <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="operational_hours"
                                   name="operational_hours"
                                   value="{{ old('operational_hours', $workshop->operational_hours) }}"
                                   required
                                   placeholder="Contoh: Senin - Sabtu (08:00 - 17:00)"
                                   class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                            @error('operational_hours')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $workshop->phone) }}"
                                   required
                                   class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                            @error('phone')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $workshop->email) }}"
                                   required
                                   class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                            @error('email')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="pt-4 border-t border-zinc-800">
                    <label for="description" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Deskripsi Bengkel
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Ceritakan secara singkat mengenai spesialisasi, layanan, atau keunggulan bengkel Anda..."
                              class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">{{ old('description', $workshop->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ── Section 2: Lokasi & Alamat ── --}}
                <div class="pt-6 border-t border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4">Lokasi & Alamat</h3>
                    <div class="space-y-4">
                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address"
                                      name="address"
                                      rows="3"
                                      required
                                      class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">{{ old('address', $workshop->address) }}</textarea>
                            @error('address')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            {{-- City --}}
                            <div>
                                <label for="city" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Kota/Kabupaten <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="city"
                                       name="city"
                                       value="{{ old('city', $workshop->city) }}"
                                       required
                                       class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                                @error('city')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Province --}}
                            <div>
                                <label for="province" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Provinsi <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="province"
                                       name="province"
                                       value="{{ old('province', $workshop->province) }}"
                                       required
                                       class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                                @error('province')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Postal Code --}}
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Kode Pos
                                </label>
                                <input type="text"
                                       id="postal_code"
                                       name="postal_code"
                                       value="{{ old('postal_code', $workshop->postal_code) }}"
                                       class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                                @error('postal_code')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Geolocation Coordinates --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                            {{-- Latitude --}}
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Garis Lintang (Latitude)
                                </label>
                                <input type="number"
                                       id="latitude"
                                       name="latitude"
                                       value="{{ old('latitude', $workshop->latitude) }}"
                                       step="0.00000001"
                                       placeholder="Contoh: -6.2088"
                                       class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                                @error('latitude')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Longitude --}}
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-zinc-300 mb-1.5">
                                    Garis Bujur (Longitude)
                                </label>
                                <input type="number"
                                       id="longitude"
                                       name="longitude"
                                       value="{{ old('longitude', $workshop->longitude) }}"
                                       step="0.00000001"
                                       placeholder="Contoh: 106.8456"
                                       class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                                @error('longitude')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-6 border-t border-zinc-800">
                    <button type="submit"
                            class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-red-900/30 transition-all text-sm">
                        Simpan Profil
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="px-5 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 font-medium rounded-xl transition-all text-sm">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>
