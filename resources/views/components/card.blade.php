@props([
    'variant' => 'default', // default | sm | lg | hoverable | flat
    'class'   => '',
])

@php
    $classes = match($variant) {
        'sm'        => 'card card-sm',
        'lg'        => 'card card-lg',
        'hoverable' => 'card cursor-pointer',
        'flat'      => 'rounded-xl p-5 bg-dark-surface',
        default     => 'card',
    };
@endphp

<div {{ $attributes->merge(['class' => $classes . ' ' . $class]) }}>
    {{ $slot }}
</div>
