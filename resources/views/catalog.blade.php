@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-12">

    <h1 class="font-display text-4xl text-sage-dark">
        Catálogo
    </h1>

    <p class="mt-4 text-sage-dark/70">
        Aquí va el listado de eventos con filtros.
    </p>

    <div class="mt-10">
        <livewire:catalog.events-grid />
    </div>

</div>

@endsection
