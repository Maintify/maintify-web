<x-app-layout>
    @slot('pageTitle', 'Audit Logs')
    @slot('breadcrumb', 'Admin / Audit Logs')

    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Audit Logs</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Catatan aktivitas sistem secara komprehensif (Append-Only & Read-Only).</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-900 border border-zinc-800 text-zinc-400">
                Total: {{ $logs->total() }} Log
            </span>
        </div>

        {{-- Filters panel --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl p-4 mb-6">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Actor search --}}
                    <div>
                        <label class="text-xs text-zinc-500 block mb-1">Aktor (Nama/Email)</label>
                        <input type="text"
                               name="actor_search"
                               value="{{ $actorSearch }}"
                               placeholder="Cari nama atau email aktor..."
                               class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 placeholder-zinc-500 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    </div>

                    {{-- Action filter --}}
                    <div>
                        <label class="text-xs text-zinc-500 block mb-1">Tipe Aksi</label>
                        <select name="action"
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $act)
                                <option value="{{ $act }}" {{ $actionFilter === $act ? 'selected' : '' }}>
                                    {{ $act }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Entity filter --}}
                    <div>
                        <label class="text-xs text-zinc-500 block mb-1">Tipe Entitas</label>
                        <select name="entity_type"
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                            <option value="">Semua Entitas</option>
                            @foreach($entityTypes as $ent)
                                <option value="{{ $ent }}" {{ $entityFilter === $ent ? 'selected' : '' }}>
                                    {{ $ent }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Start Date --}}
                    <div>
                        <label class="text-xs text-zinc-500 block mb-1">Tanggal Mulai</label>
                        <input type="date"
                               name="start_date"
                               value="{{ $startDate }}"
                               class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="text-xs text-zinc-500 block mb-1">Tanggal Selesai</label>
                        <input type="date"
                               name="end_date"
                               value="{{ $endDate }}"
                               class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-red-500 transition-colors">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-sm font-semibold rounded-xl transition-all">
                            Filter
                        </button>
                        @if($actorSearch || $actionFilter || $entityFilter || $startDate || $endDate)
                            <a href="{{ route('admin.audit-logs.index') }}"
                               class="px-4 py-2 bg-zinc-900 hover:bg-zinc-850 border border-zinc-800 text-zinc-400 hover:text-zinc-200 text-sm font-medium rounded-xl transition-all flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Audit Logs Table --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#2E3030] bg-[#1d1f1f]">
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Waktu</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Aktor</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Aksi</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">Entitas Terkait</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400">IP Address</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-zinc-400 text-right">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2E3030]">
                        @forelse($logs as $log)
                            <tr class="hover:bg-zinc-900/30 transition-colors align-middle">
                                {{-- Timestamp --}}
                                <td class="px-6 py-4 text-sm text-zinc-450 font-mono">
                                    {{ $log->created_at->translatedFormat('d M Y H:i:s') }}
                                </td>
                                {{-- Actor --}}
                                <td class="px-6 py-4">
                                    @if($log->actor)
                                        <div class="text-sm font-semibold text-zinc-300">{{ $log->actor->name }}</div>
                                        <div class="text-xs text-zinc-500 mt-0.5">{{ $log->actor->email }}</div>
                                    @else
                                        <span class="text-zinc-650 text-xs italic">System / Cron</span>
                                    @endif
                                </td>
                                {{-- Action Type --}}
                                <td class="px-6 py-4 text-sm text-zinc-200 font-semibold font-mono">
                                    {{ $log->action }}
                                </td>
                                {{-- Entity Type & ID --}}
                                <td class="px-6 py-4 text-sm text-zinc-350 font-mono">
                                    @if($log->entity_type)
                                        @php
                                            $routeMap = [
                                                'users' => 'admin.users.show',
                                                'workshops' => 'admin.workshops.show',
                                                'vehicles' => 'admin.vehicles.show',
                                            ];
                                            $routeName = $routeMap[$log->entity_type] ?? null;
                                        @endphp
                                        @if($routeName && Route::has($routeName))
                                            <a href="{{ route($routeName, $log->entity_id) }}" class="text-red-400 hover:underline">
                                                {{ $log->entity_type }} #{{ $log->entity_id }}
                                            </a>
                                        @else
                                            {{ $log->entity_type }} #{{ $log->entity_id }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                {{-- IP Address --}}
                                <td class="px-6 py-4 text-xs font-mono text-zinc-500">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-xs font-bold rounded-lg transition-all shadow-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                    </svg>
                                    <p class="text-zinc-400 text-sm font-semibold">Tidak Ada Log Audit</p>
                                    <p class="text-zinc-650 text-xs mt-1">Tidak ada data log aktivitas yang tercatat atau cocok dengan kriteria filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
