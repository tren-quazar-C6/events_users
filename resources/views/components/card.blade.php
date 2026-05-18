@props([])

@php
    $base = 'bg-white rounded-card shadow-soft p-6';
@endphp

<div {{ $attributes->class($base) }}>
    {{ $slot }}
</div>