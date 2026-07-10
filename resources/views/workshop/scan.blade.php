<x-app-layout>
    @slot('pageTitle', 'Scan QR Code')
    @slot('breadcrumb', 'Workshop / Scanner')

    @push('head')
        {{-- Import HTML5 QR Code Scanner Library from CDN --}}
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        @vite(['resources/js/qr-scanner.js'])
        
        <style>
            .viewfinder-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 1rem;
                background-color: #181A1A;
                border: 1px solid #2E3030;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            }
            .scan-glow {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, transparent, #ef4444, transparent);
                animation: scan-line-animation 2.5s infinite linear;
                z-index: 10;
                pointer-events: none;
            }
            @keyframes scan-line-animation {
                0% { top: 0%; }
                50% { top: 100%; }
                100% { top: 0%; }
            }
            .viewfinder-corner {
                position: absolute;
                width: 20px;
                height: 20px;
                border-color: #ef4444;
                border-style: solid;
                pointer-events: none;
                z-index: 10;
            }
            .corner-tl { top: 15px; left: 15px; border-width: 3px 0 0 3px; }
            .corner-tr { top: 15px; right: 15px; border-width: 3px 3px 0 0; }
            .corner-bl { bottom: 15px; left: 15px; border-width: 0 0 3px 3px; }
            .corner-br { bottom: 15px; right: 15px; border-width: 0 3px 3px 0; }
        </style>
    @endpush

    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Card Container --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 md:p-8 flex flex-col items-center">
            
            {{-- Header info --}}
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-zinc-100 mb-2">Scan QR Code Kendaraan</h2>
                <p class="text-sm text-zinc-400 max-w-md">Arahkan kamera pada QR Code Maintify yang terpasang di kendaraan untuk memverifikasi data dan merekam riwayat service.</p>
            </div>

            {{-- Viewfinder Area --}}
            <div class="viewfinder-wrapper w-full max-w-md aspect-square mb-6 relative">
                <div class="scan-glow" id="scan-glow-line" style="display: none;"></div>
                
                {{-- Corners decoration --}}
                <div class="viewfinder-corner corner-tl"></div>
                <div class="viewfinder-corner corner-tr"></div>
                <div class="viewfinder-corner corner-bl"></div>
                <div class="viewfinder-corner corner-br"></div>
                
                {{-- QR code render target --}}
                <div id="qr-reader" class="w-full h-full border-0 bg-zinc-900 flex items-center justify-center">
                    <div class="text-center p-6">
                        <svg class="w-16 h-16 text-zinc-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-xs text-zinc-500 font-medium">Kamera Belum Aktif</p>
                    </div>
                </div>
            </div>

            {{-- Scanner Status & Actions --}}
            <div class="w-full max-w-md text-center mb-4">
                <p id="scanner-status" class="text-zinc-500 text-sm mb-4">Scanner dinonaktifkan.</p>
                
                <div class="flex justify-center gap-4">
                    <button id="start-scan-btn" 
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-red-900/30 transition-all flex items-center gap-2"
                            onclick="document.getElementById('scan-glow-line').style.display = 'block';">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Aktifkan Kamera
                    </button>
                    
                    <button id="stop-scan-btn" 
                            class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-semibold rounded-lg border border-zinc-700 transition-all hidden"
                            onclick="document.getElementById('scan-glow-line').style.display = 'none';">
                        Matikan Kamera
                    </button>
                </div>
            </div>
        </div>

        {{-- Verification Result Modal --}}
        <div id="scan-result-modal" class="fixed inset-0 z-50 items-center justify-center hidden" style="background-color: rgba(0,0,0,0.8);">
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl w-full max-w-lg mx-4 p-6 shadow-2xl relative animate-scale-up">
                
                {{-- Header --}}
                <div class="flex justify-between items-center pb-4 mb-6 border-b border-zinc-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h3 class="text-lg font-bold text-zinc-100">Kendaraan Terverifikasi</h3>
                    </div>
                    <button id="close-modal-btn" class="text-zinc-400 hover:text-zinc-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body / Vehicle Details --}}
                <div class="space-y-4 mb-8">
                    {{-- Full Brand Model --}}
                    <div>
                        <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Kendaraan</span>
                        <span id="res-brand-model" class="text-xl font-bold text-zinc-100">-</span>
                    </div>

                    {{-- Double Column --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Plat Nomor</span>
                            <span id="res-plate" class="text-sm font-bold text-red-400 tracking-wide bg-red-950/30 border border-red-900/50 px-2.5 py-1 rounded inline-block">-</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Identitas QR (VIN)</span>
                            <span id="res-vin" class="text-sm font-medium text-zinc-300 block overflow-hidden overflow-ellipsis whitespace-nowrap">-</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Pemilik</span>
                            <span id="res-owner" class="text-sm font-semibold text-zinc-300">-</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Status Kesehatan</span>
                            <span id="res-health" class="inline-block">-</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Odometer</span>
                            <span id="res-odometer" class="text-sm font-semibold text-zinc-300">-</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider block mb-1">Kondisi Oli</span>
                            <span id="res-oil-life" class="text-sm font-semibold text-zinc-300">-</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex justify-end gap-3">
                    <a id="res-action-btn" href="#"
                       class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-lg hover:shadow-red-900/20 transition-all flex items-center gap-2">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Catat Service Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
