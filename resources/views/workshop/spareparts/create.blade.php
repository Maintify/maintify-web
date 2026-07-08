<x-app-layout>
    @slot('pageTitle', 'Tambah Sparepart')
    @slot('breadcrumb', 'Workshop / Spareparts / Tambah')

    <div class="max-w-xl mx-auto px-4 py-8">

        {{-- ── Header ── --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('workshop.spareparts.index') }}"
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 hover:border-zinc-500 transition-colors">
                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Tambah Sparepart</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Tambahkan sparepart baru ke dalam katalog bengkel Anda</p>
            </div>
        </div>

        {{-- ── Form Card ── --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-xl">
            <form method="POST" action="{{ route('workshop.spareparts.store') }}" class="space-y-5">
                @csrf

                {{-- Sparepart Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Nama Sparepart <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contoh: Oli Shell Helix HX7 10W-40, Kampas Rem Belakang"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Kategori
                    </label>
                    <input type="text"
                           id="category"
                           name="category"
                           value="{{ old('category') }}"
                           placeholder="Contoh: Oli, Rem, Kelistrikan, Filter"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('category')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Price --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="price"
                           name="price"
                           value="{{ old('price') }}"
                           required
                           min="0"
                           step="500"
                           placeholder="Contoh: 95000"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-650 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    @error('price')
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
                               checked
                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-zinc-700 bg-zinc-950 rounded">
                    </div>
                    <div class="text-sm">
                        <label for="is_active" class="font-medium text-zinc-300">Aktif</label>
                        <p class="text-zinc-500 text-xs">Aktifkan agar sparepart ini muncul di formulir input service</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-zinc-800">
                    <button type="submit"
                            class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-red-900/30 transition-all text-sm">
                        Simpan Sparepart
                    </button>
                    <a href="{{ route('workshop.spareparts.index') }}"
                       class="px-5 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 font-medium rounded-xl transition-all text-sm">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>
</x-app-layout>
