@props([
    'hover' => true,
    'padding' => 'p-6'
])

@php
    $base = '
        bg-white
        rounded-3xl
        border border-slate-100
        shadow-sm
        transition-all
        duration-300
        overflow-hidden
    ';

    $hoverStyles = $hover
        ? 'hover:-translate-y-1 hover:shadow-xl'
        : '';
@endphp

<div {{ $attributes->class("$base $hoverStyles $padding") }}>
    {{ $slot }}
</div>

