<nav class="bg-white shadow-soft sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="font-display text-2xl text-sage-dark">
                Tickify
            </a>

            {{-- Nav desktop (oculto en móvil — la bottom nav lo reemplaza) --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-sage-dark/80 hover:text-sage transition">Inicio</a>
                <a href="{{ route('catalog') }}" class="text-sage-dark/80 hover:text-sage transition">Catálogo</a>

                @auth
                    {{-- Usuario logueado: dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-sage-dark font-semibold">
                            {{ auth()->user()->name }}
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-card shadow-soft py-2">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-cream">Mi dashboard</a>
                            <a href="{{ route('dashboard.tickets') }}" class="block px-4 py-2 text-sm hover:bg-cream">Mis entradas</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-coral hover:bg-cream">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Guest: login + register --}}
                    <a href="{{ route('login') }}" class="text-sage-dark/80 hover:text-sage transition">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="bg-sage text-white px-4 py-2 rounded-btn font-semibold hover:bg-sage-dark transition">
                        Crear cuenta
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>