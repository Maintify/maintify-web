@props([
    'type' => 'success', // success | error | warning | info
    'message' => null,
    'duration' => 4000,
])

@php
    $message = $message ?? session('success') ?? session('error') ?? session('warning') ?? session('status') ?? session('info');
    $type = session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') || session('status') ? 'info' : $type));
@endphp

@if($message)
<div
    x-data="{ 
        show: false,
        init() {
            setTimeout(() => { this.show = true; }, 100);
            if ({{ $duration }} > 0) {
                setTimeout(() => { this.show = false; }, {{ $duration }});
            }
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="transform translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed bottom-4 right-4 z-50 max-w-md w-full sm:w-auto bg-zinc-900 border rounded-xl shadow-2xl p-4 transition-all"
    :class="{
        'border-emerald-500/30 bg-emerald-950/20 text-emerald-400': '{{ $type }}' === 'success',
        'border-red-500/30 bg-red-950/20 text-red-400': '{{ $type }}' === 'error',
        'border-amber-500/30 bg-amber-950/20 text-amber-400': '{{ $type }}' === 'warning',
        'border-blue-500/30 bg-blue-950/20 text-blue-400': '{{ $type }}' === 'info',
    }"
    role="alert"
>
    <div class="flex items-start gap-3">
        <!-- Icon -->
        <div class="flex-shrink-0 mt-0.5">
            @if($type === 'success')
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @elseif($type === 'error')
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @elseif($type === 'warning')
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @endif
        </div>

        <!-- Content -->
        <div class="flex-grow">
            <p class="text-sm font-medium">{{ $message }}</p>
        </div>

        <!-- Close Button -->
        <button @click="show = false" class="flex-shrink-0 text-zinc-400 hover:text-zinc-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif
