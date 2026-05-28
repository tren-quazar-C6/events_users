@extends('layouts.dashboard')

@section('title', 'Mis favoritos')

@section('dashboard-content')
    <h1 class="font-display text-4xl text-sage-dark mb-6">Mis favoritos</h1>
    <livewire:favorite-list />
@endsection
