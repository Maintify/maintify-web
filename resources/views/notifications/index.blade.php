<x-app-layout>
    @slot('pageTitle', 'Pusat Notifikasi')
    @slot('breadcrumb', 'Pusat Notifikasi')

    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-100 tracking-tight">Pusat Notifikasi</h1>
                <p class="text-sm text-zinc-500 mt-0.5">Pantau pemberitahuan servis, persetujuan bengkel, dan transfer kepemilikan.</p>
            </div>
            
            @php
                $hasUnread = $notifications->contains('is_read', false);
            @endphp
            @if($hasUnread)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-zinc-100 text-xs font-semibold rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Success Flash Messages --}}
        @if(session('success'))
            <div style="background-color: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Notifications List --}}
        <div class="bg-[#181A1A] border border-[#2E3030] rounded-2xl overflow-hidden shadow-lg">
            <div class="divide-y divide-[#2E3030]">
                @forelse($notifications as $notification)
                    <div class="p-5 flex gap-4 hover:bg-zinc-900/10 transition-colors {{ !$notification->is_read ? 'bg-zinc-900/20' : '' }}">
                        {{-- Unread Dot Indicator --}}
                        <div class="flex-shrink-0 mt-1">
                            @if(!$notification->is_read)
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                            @else
                                <span class="block h-3 w-3 rounded-full bg-zinc-700"></span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-250 {{ !$notification->is_read ? 'text-zinc-100 font-extrabold' : '' }}">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                                        {{ $notification->message }}
                                    </p>
                                    <span class="text-[10px] text-zinc-500 mt-2 block font-mono">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Mark as Read Button --}}
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <button type="submit"
                                                title="Tandai telah dibaca"
                                                class="p-1.5 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-650 text-zinc-400 hover:text-zinc-200 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-zinc-400 text-sm font-semibold">Tidak Ada Notifikasi</p>
                        <p class="text-zinc-650 text-xs mt-1">Anda sudah membaca semua pemberitahuan sistem.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-[#2E3030] bg-[#1d1f1f]">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
