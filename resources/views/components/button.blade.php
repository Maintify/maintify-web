@props([
    'variant' => 'primary',  // primary | secondary | ghost | danger
    'size'    => 'md',       // xs | sm | md | lg | xl
    'type'    => 'button',
    'href'    => null,
    'icon'    => false,
])

@php
    $variantClass = match($variant) {
        'secondary' => 'btn-secondary',
        'ghost'     => 'btn-ghost',
        'danger'    => 'btn-danger',
        default     => 'btn-primary',
    };

    $sizeClass = match($size) {
        'xs' => 'btn-xs',
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        'xl' => 'btn-xl',
        default => '',
    };

    $iconClass = $icon ? 'btn-icon' : '';

    $classes = trim(implode(' ', array_filter([$variantClass, $sizeClass, $iconClass])));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
