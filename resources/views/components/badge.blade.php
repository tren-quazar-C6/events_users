@props([
    'variant' => 'default',   // default | success | error
])

@php
    // Base que TODO badge comparte: pequeño, redondeado tipo píldora
    $base = 'inline-flex items-center px-2 py-1 text-xs font-medium rounded-full';

    $variants = [
        'default' => 'bg-sage-light text-sage-dark',
        'success' => 'bg-sage text-white',
        'error'   => 'bg-coral text-white',
    ];

    $classes = $base.' '.$variants[$variant];
@endphp

<span {{ $attributes->class($classes) }}>
    {{ $slot }}
</span>