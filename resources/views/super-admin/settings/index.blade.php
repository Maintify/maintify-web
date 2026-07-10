<x-app-layout>
    @slot('pageTitle', 'Global Settings')
    @slot('breadcrumb', 'Admin / Settings')

    <div class="max-w-3xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Global Settings</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Konfigurasi parameter global platform Maintify, pengingat servis kendaraan, dan durasi kedaluwarsa transfer kepemilikan.</p>
        </div>

        {{-- Success / Error Flash Messages --}}
        @if(session('success'))
            <div style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px;">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Panel --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Service Reminder Days --}}
                <div class="space-y-1.5">
                    <label for="service_reminder_interval" class="text-sm font-semibold text-zinc-200 block">
                        Interval Pengingat Servis (Hari)
                    </label>
                    <input type="number"
                           id="service_reminder_interval"
                           name="service_reminder_interval"
                           value="{{ $settings['service_reminder_interval'] }}"
                           required
                           min="1"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <p class="text-[11px] text-zinc-500">Jumlah hari setelah service terakhir sebelum sistem menjadwalkan service berikutnya secara otomatis.</p>
                </div>

                {{-- Service Reminder Mileage --}}
                <div class="space-y-1.5 border-t border-[#2E3030]/50 pt-4">
                    <label for="service_reminder_mileage" class="text-sm font-semibold text-zinc-200 block">
                        Interval Pengingat Servis (Odometer - Km)
                    </label>
                    <input type="number"
                           id="service_reminder_mileage"
                           name="service_reminder_mileage"
                           value="{{ $settings['service_reminder_mileage'] }}"
                           required
                           min="1"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <p class="text-[11px] text-zinc-500">Selisih odometer (km) setelah service terakhir sebelum sistem menyarankan service berikutnya.</p>
                </div>

                {{-- Transfer Expiry days --}}
                <div class="space-y-1.5 border-t border-[#2E3030]/50 pt-4">
                    <label for="transfer_expiry_days" class="text-sm font-semibold text-zinc-200 block">
                        Masa Kedaluwarsa Transfer Kepemilikan (Hari)
                    </label>
                    <input type="number"
                           id="transfer_expiry_days"
                           name="transfer_expiry_days"
                           value="{{ $settings['transfer_expiry_days'] }}"
                           required
                           min="1"
                           class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    <p class="text-[11px] text-zinc-500">Jumlah hari sebelum permintaan transfer kepemilikan kendaraan secara otomatis kedaluwarsa dan dinonaktifkan.</p>
                </div>

                <div class="border-t border-[#2E3030]/50 pt-6 flex justify-end">
                    <button type="submit"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
