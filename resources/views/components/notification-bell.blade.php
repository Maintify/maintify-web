@php
    $unreadCount = auth()->check() ? auth()->user()->notifications()->unread()->count() : 0;
    $recentNotifications = auth()->check() ? auth()->user()->notifications()->orderBy('created_at', 'desc')->take(5)->get() : collect();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" style="display: inline-block;">
    {{-- Bell Icon Button --}}
    <button @click="open = !open" 
            class="relative p-2 text-zinc-400 hover:text-zinc-200 transition-colors focus:outline-none rounded-xl hover:bg-zinc-800/50 flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[9px] font-bold text-white items-center justify-center leading-none">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-[#181A1A] border border-[#2E3030] rounded-2xl shadow-xl overflow-hidden z-50 py-1"
         style="display: none;">
        
        {{-- Dropdown Header --}}
        <div class="px-4 py-3 border-b border-[#2E3030] flex items-center justify-between">
            <span class="text-xs font-bold text-zinc-300">Notifikasi</span>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] text-red-400 hover:text-red-300 font-semibold transition-colors">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Dropdown Items List --}}
        <div class="max-h-64 overflow-y-auto divide-y divide-[#2E3030]/60">
            @forelse($recentNotifications as $notification)
                <div class="px-4 py-3 hover:bg-zinc-900/40 transition-colors {{ !$notification->is_read ? 'bg-zinc-900/20' : '' }} flex items-start gap-2.5">
                    {{-- Status Indicator Dot --}}
                    @if(!$notification->is_read)
                        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-red-500 mt-1.5"></span>
                    @else
                        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-zinc-700 mt-1.5"></span>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-1">
                            <h4 class="text-xs font-bold text-zinc-300 truncate leading-snug {{ !$notification->is_read ? 'text-zinc-150 font-extrabold' : '' }}">
                                {{ $notification->title }}
                            </h4>
                            @if(!$notification->is_read)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="text-[9px] text-zinc-500 hover:text-zinc-300 font-medium hover:underline">
                                        Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                        <p class="text-[11px] text-zinc-400 mt-0.5 line-clamp-2 leading-relaxed">
                            {{ $notification->message }}
                        </p>
                        <span class="text-[9px] text-zinc-500 mt-1 block font-mono">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-zinc-500">
                    <svg class="w-8 h-8 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-xs">Tidak ada notifikasi baru</span>
                </div>
            @endforelse
        </div>

        {{-- Dropdown Footer --}}
        <a href="{{ route('notifications.index') }}"
           class="block px-4 py-2 border-t border-[#2E3030] text-center text-[10px] font-bold text-zinc-400 hover:text-zinc-200 transition-colors bg-zinc-950/20 hover:bg-zinc-950/40">
            Lihat Semua Notifikasi
        </a>
    </div>
</div>
