@extends('layouts.app')
@section('title', 'Mi Dashboard')
@section('content')
    <div class="max-w-5xl mx-auto px-4 py-12">
        <h1 class="font-display text-4xl text-sage-dark">¡Hola, {{ auth()->user()->name }}!</h1>
        <p class="mt-2 text-sage-dark/70">Tu cuenta está lista. Próximamente vas a ver aquí tus eventos favoritos.</p>
    </div>
@endsection