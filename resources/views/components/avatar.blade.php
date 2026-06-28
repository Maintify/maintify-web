@props([
    'name'   => null,
    'size'   => 'md',   // xs | sm | md | lg | xl
    'src'    => null,
])

@php
    $initial = $name ? strtoupper(substr($name, 0, 1)) : '?';
    $sizeClass = match($size) {
        'xs' => 'avatar-xs',
        'sm' => 'avatar-sm',
        'lg' => 'avatar-lg',
        'xl' => 'avatar-xl',
        default => 'avatar-md',
    };
@endphp

<div {{ $attributes->merge(['class' => $sizeClass]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $name ?? 'Avatar' }}" class="w-full h-full object-cover">
    @else
        {{ $initial }}
    @endif
</div>
