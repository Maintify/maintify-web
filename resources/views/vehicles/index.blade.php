<x-app-layout>
    @slot('pageTitle', 'My Vehicles')

    <div style="max-width: 1200px; margin: 0 auto; padding: 24px 16px;">
        {{-- Header & Add Action --}}
        <div style="display: flex; flex-direction: column; md-flex-direction: row; justify-content: space-between; align-items: flex-start; md-align-items: center; gap: 16px; margin-bottom: 28px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; color: #F4F4F5; letter-spacing: -0.02em; margin: 0 0 6px;">Garasi Saya </h1>
                <p style="color: #71717A; font-size: 14px; margin: 0;">Kelola dan pantau kondisi semua kendaraan Anda di satu tempat.</p>
            </div>
            
            @if($vehicles->isNotEmpty() || !empty($search))
                <a href="{{ route('vehicles.create') }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 20px rgba(65,0,8,0.4);">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kendaraan
                </a>
            @endif
        </div>

        {{-- Search Bar --}}
        @if($vehicles->isNotEmpty() || !empty($search))
            <div style="margin-bottom: 28px; max-width: 480px;">
                <form method="GET" action="{{ route('vehicles.index') }}">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari kendaraan berdasarkan merek, model, atau plat nomor..."
                            class="search-input"
                            style="background-color: #1A1C1C; border-color: #2E3030;"
                        />
                        @if(!empty($search))
                            <a href="{{ route('vehicles.index') }}" style="position: absolute; right: 12px; color: #71717A; display: flex; align-items: center; justify-content: center;" title="Hapus pencarian">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        @endif

        {{-- Main Content area --}}
        @if($vehicles->isEmpty())
            {{-- Case: Empty States --}}
            @if(!empty($search))
                {{-- Search Empty State --}}
                <div class="empty-state" style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 20px; padding: 48px 24px;">
                    <div class="empty-state-icon" style="background-color: rgba(239, 68, 68, 0.1); color: #f87171;">
                        <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="empty-state-title" style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Pencarian Tidak Ditemukan</h3>
                    <p class="empty-state-desc" style="max-width: 380px; margin-bottom: 24px;">Tidak ditemukan kendaraan yang cocok dengan kata kunci "{{ $search }}".</p>
                    <a href="{{ route('vehicles.index') }}" class="btn-secondary" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none;">
                        Reset Pencarian
                    </a>
                </div>
            @else
                {{-- Overall Empty State --}}
                <div class="empty-state" style="background-color: #1A1C1C; border: 1px solid #2E3030; border-radius: 20px; padding: 64px 24px;">
                    <div class="empty-state-icon" style="background-color: rgba(65, 0, 8, 0.2); color: #ff9aa4;">
                        <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2z"/>
                        </svg>
                    </div>
                    <h3 class="empty-state-title" style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Garasi Kosong</h3>
                    <p class="empty-state-desc" style="max-width: 380px; margin-bottom: 24px;">Anda belum memiliki kendaraan terdaftar.</p>
                    <a href="{{ route('vehicles.create') }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 20px rgba(65,0,8,0.4);">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Kendaraan
                    </a>
                </div>
            @endif
        @else
            {{-- Cards Grid Layout --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                @foreach($vehicles as $vehicle)
                    @include('vehicles.partials.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
