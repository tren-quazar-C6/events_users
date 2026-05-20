@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid md:grid-cols-[260px_1fr] gap-8">

            @include('partials.dashboard-sidebar')

            <div class="min-w-0">
                {{-- Flash messages (después de guardar perfil etc.) --}}
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-card bg-sage-light text-sage-dark border border-sage/30">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('dashboard-content')
            </div>

        </div>
    </div>
@endsection