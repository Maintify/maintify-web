<x-app-layout>
    @slot('pageTitle', 'Cari Bengkel Terdekat')
    @slot('breadcrumb', 'Bengkel / Terdekat')

    {{-- Leaflet Assets CDN --}}
    @slot('additionalHead')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <style>
            .pulse {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
                }
                70% {
                    transform: scale(1);
                    box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
                }
                100% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
                }
            }
            /* Override Leaflet Popup Styles for Dark Theme Match */
            .leaflet-popup-content-wrapper {
                background-color: #181a1a !important;
                color: #f4f4f5 !important;
                border: 1px solid #2e3030 !important;
                border-radius: 12px !important;
            }
            .leaflet-popup-tip {
                background-color: #181a1a !important;
                border: 1px solid #2e3030 !important;
            }
        </style>
    @endslot

    <div class="flex flex-col md:flex-row h-[calc(100vh-64px)] overflow-hidden">
        
        {{-- ── Sidebar (Filters & List) ── --}}
        <div class="w-full md:w-[400px] border-r border-[#2E3030] bg-[#121414] flex flex-col flex-shrink-0 overflow-y-auto">
            {{-- Filters Header --}}
            <div class="p-5 border-b border-[#2E3030] space-y-4">
                <div>
                    <h1 class="text-lg font-bold text-zinc-100 tracking-tight">Cari Bengkel</h1>
                    <p class="text-xs text-zinc-500 mt-0.5">Temukan bengkel terdekat di sekitar lokasi Anda</p>
                </div>

                <div class="space-y-3">
                    {{-- Radius Filter --}}
                    <div>
                        <label for="filter-radius" class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Jarak Maksimal</label>
                        <select id="filter-radius"
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-red-500 transition-colors">
                            <option value="2">2 km</option>
                            <option value="5">5 km</option>
                            <option value="10" selected>10 km</option>
                            <option value="20">20 km</option>
                            <option value="50">50 km</option>
                        </select>
                    </div>

                    {{-- Service Type Filter --}}
                    <div>
                        <label for="filter-service-type" class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Jenis Layanan</label>
                        <select id="filter-service-type"
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-red-500 transition-colors">
                            <option value="">Semua Layanan</option>
                            @foreach($serviceTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rating Filter --}}
                    <div>
                        <label for="filter-rating" class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Rating Minimal</label>
                        <select id="filter-rating"
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-red-500 transition-colors">
                            <option value="">Semua Rating</option>
                            <option value="4.5">★ 4.5 ke atas</option>
                            <option value="4.0">★ 4.0 ke atas</option>
                            <option value="3.0">★ 3.0 ke atas</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Results Content Container --}}
            <div class="flex-1 p-5 relative min-h-[300px]">
                {{-- Loading State --}}
                <div id="loading-state" class="absolute inset-0 bg-[#121414]/90 flex flex-col items-center justify-center hidden">
                    <svg class="animate-spin h-8 w-8 text-red-500 mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs text-zinc-400 font-semibold">Mencari bengkel mitra...</p>
                </div>

                {{-- Empty State --}}
                <div id="empty-state" class="absolute inset-0 bg-[#121414]/90 flex flex-col items-center justify-center text-center px-6 hidden">
                    <svg class="w-12 h-12 text-zinc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-zinc-400 text-sm font-semibold">Bengkel Tidak Ditemukan</p>
                    <p class="text-zinc-650 text-xs mt-1">Coba perbesar jarak pencarian atau ubah filter filter di atas.</p>
                </div>

                {{-- List Container --}}
                <div id="workshop-list" class="space-y-4">
                    {{-- Dynamically loaded --}}
                </div>
            </div>
        </div>

        {{-- ── Map Panel ── --}}
        <div class="flex-1 relative h-full">
            <div id="map" class="w-full h-full z-0"></div>
        </div>

    </div>

    @vite('resources/js/workshop-map.js')
</x-app-layout>
