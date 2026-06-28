@props([
    'variant' => 'neutral', // primary | success | warning | danger | neutral
    'dot'     => false,
])

@php
    $variantClass = match($variant) {
        'primary' => 'badge-primary',
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger'  => 'badge-danger',
        default   => 'badge-neutral',
    };
@endphp

<span {{ $attributes->merge(['class' => $variantClass]) }}>
    @if($dot)
        <span class="badge-dot"></span>
    @endif
    {{ $slot }}
</span>
