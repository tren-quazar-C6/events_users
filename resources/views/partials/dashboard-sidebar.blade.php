@php
    // Helper para marcar el ítem activo según la ruta actual
    $isActive = fn ($routeName) => request()->routeIs($routeName)
        ? 'bg-sage-light text-sage-dark font-semibold'
        : 'text-sage-dark/70 hover:bg-cream hover:text-sage-dark';
@endphp

<aside>
    <div class="bg-white rounded-card shadow-soft p-4 sticky top-24">
        <p class="px-3 pb-3 text-xs font-semibold uppercase tracking-wide text-sage-dark/50">
            Mi cuenta
        </p>

        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-btn transition {{ $isActive('dashboard') }}">
                📊 Resumen
            </a>

            <a href="{{ route('dashboard.tickets') }}"
               class="block px-3 py-2 rounded-btn transition {{ $isActive('dashboard.tickets') }}">
                🎟️ Mis entradas
            </a>

            <a href="{{ route('dashboard.history') }}"
               class="block px-3 py-2 rounded-btn transition {{ $isActive('dashboard.history') }}">
                📜 Historial
            </a>

            <a href="{{ route('dashboard.profile') }}"
               class="block px-3 py-2 rounded-btn transition {{ $isActive('dashboard.profile') }}">
                👤 Mi perfil
            </a>
        </nav>

        <hr class="my-4 border-sage-dark/10">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 rounded-btn text-coral hover:bg-cream transition">
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>