@extends('layouts.dashboard')

@section('title', 'Mis favoritos')

@section('dashboard-content')
<div>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <h1 class="font-display text-4xl text-sage-dark mb-1">Mis favoritos</h1>
            <p class="text-sage-dark/70">Los eventos que guardaste para no perderte.</p>
        </div>
        <a href="{{ route('catalog') }}"
           class="self-start px-5 py-2 border border-sage text-sage text-sm font-semibold rounded-btn hover:bg-sage hover:text-white transition-all">
            Ver cartelera completa
        </a>
    </div>

    <livewire:favorite-list />
</div>
@endsection
