@props([
    'label'       => null,
    'placeholder' => '',
    'type'        => 'text',     // text | email | password | tel | number
    'error'       => null,
    'name'        => null,
])

@php
    // Base que TODO input comparte
    $base = 'w-full px-4 py-3 rounded-btn border bg-white text-sage-dark transition focus:outline-none focus:ring-2 placeholder:text-sage-dark/40';

    // En lugar de "variants" como el botón, aquí hablamos de ESTADOS:
    // el borde cambia si hay error
    $stateClasses = $error
        ? 'border-coral focus:border-coral focus:ring-coral/20'
        : 'border-sage-dark/20 focus:border-sage focus:ring-sage/20';

    $classes = $base.' '.$stateClasses;
@endphp

<div class="w-full">
    @if ($label)
        <label class="block text-sm font-semibold text-sage-dark mb-2">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" @endif
        placeholder="{{ $placeholder }}"
        {{ $attributes->class($classes) }}
    >

    @if ($error)
        <p class="mt-1 text-sm text-coral">{{ $error }}</p>
    @endif
</div>