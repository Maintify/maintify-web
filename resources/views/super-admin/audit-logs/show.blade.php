<x-app-layout>
    @slot('pageTitle', 'Detail Log Audit - ' . $auditLog->id)
    @slot('breadcrumb', 'Admin / Audit Logs / Detail')

    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Back Button & Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-400 hover:text-zinc-200 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Log
            </a>
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Rincian Log Audit</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Detail informasi metadata transaksi dan data teknis terkait rekaman log.</p>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Overview Grid Card --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                <h3 class="text-sm font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-2">Informasi Umum</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-zinc-300">
                    <div>
                        <span class="text-xs text-zinc-500 block">Waktu Kejadian</span>
                        <span class="font-semibold text-zinc-250 font-mono mt-0.5 block">
                            {{ $auditLog->created_at->translatedFormat('d F Y H:i:s') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 block">IP Address</span>
                        <span class="font-semibold text-zinc-250 font-mono mt-0.5 block">
                            {{ $auditLog->ip_address ?? '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 block">Tipe Aksi</span>
                        <span class="font-semibold text-zinc-200 font-mono mt-0.5 block bg-zinc-950/60 border border-zinc-800 px-2.5 py-1 rounded inline-block text-xs">
                            {{ $auditLog->action }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 block">Entitas Terkait</span>
                        <span class="font-semibold text-zinc-250 font-mono mt-0.5 block">
                            @if($auditLog->entity_type)
                                @php
                                    $routeMap = [
                                        'users' => 'admin.users.show',
                                        'workshops' => 'admin.workshops.show',
                                        'vehicles' => 'admin.vehicles.show',
                                    ];
                                    $routeName = $routeMap[$auditLog->entity_type] ?? null;
                                @endphp
                                @if($routeName && Route::has($routeName))
                                    <a href="{{ route($routeName, $auditLog->entity_id) }}" class="text-red-400 hover:underline">
                                        {{ $auditLog->entity_type }} #{{ $auditLog->entity_id }}
                                    </a>
                                @else
                                    {{ $auditLog->entity_type }} #{{ $auditLog->entity_id }}
                                @endif
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actor Card --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                <h3 class="text-sm font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-2">Aktor Pelaku Aksi</h3>
                @if($auditLog->actor)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-zinc-300">
                        <div>
                            <span class="text-xs text-zinc-500 block">Nama Aktor</span>
                            <span class="font-semibold text-zinc-200">{{ $auditLog->actor->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Email Aktor</span>
                            <span class="font-semibold text-zinc-200">{{ $auditLog->actor->email }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">Peran Akses</span>
                            @if($auditLog->actor->isSuperAdmin())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950/40 border border-red-900/50 text-red-400 mt-1">
                                    Super Admin
                                </span>
                            @elseif($auditLog->actor->isWorkshop())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-950/40 border border-blue-900/50 text-blue-400 mt-1">
                                    Bengkel Mitra
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/40 border border-emerald-900/50 text-emerald-400 mt-1">
                                    Pemilik Kendaraan
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 block">ID Aktor</span>
                            <span class="font-semibold text-zinc-200 font-mono mt-1 block">#{{ $auditLog->actor->id }}</span>
                        </div>
                    </div>
                @else
                    <span class="text-zinc-650 text-xs italic">Aksi dilakukan secara otomatis oleh sistem (System/Cron).</span>
                @endif
            </div>

            {{-- Metadata Card --}}
            <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-6 shadow-lg">
                <h3 class="text-sm font-bold text-zinc-200 mb-4 border-b border-[#2E3030] pb-2">Metadata Aksi (Payload Context)</h3>
                @if($auditLog->metadata && count($auditLog->metadata) > 0)
                    <pre class="bg-zinc-950 p-5 rounded-xl border border-zinc-800 overflow-x-auto text-xs text-zinc-300 font-mono leading-relaxed"><code>{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                @else
                    <span class="text-zinc-650 text-xs italic">Tidak ada payload metadata tambahan yang tercatat untuk aksi ini.</span>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
