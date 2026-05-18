<nav x-data="{ mobileOpen: false }" class="sticky top-0 w-full z-50 bg-background/95 backdrop-blur-sm shadow-sm shadow-on-surface/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 flex items-center justify-between h-20">

        @auth
        {{-- ── Navbar autenticado ─────────────────────────────── --}}

        {{-- Izquierda: logo + links --}}
        <div class="flex items-center gap-10">
            <a href="{{ route('home') }}" class="font-display text-3xl text-primary tracking-tight leading-none">
                Butaca
            </a>

            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('catalog') }}"
                   class="font-label-lg text-label-lg font-bold transition-colors
                          {{ request()->routeIs('catalog') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-on-surface-variant hover:text-primary' }}">
                    Cartelera
                </a>
                <a href="{{ route('dashboard.tickets') }}"
                   class="font-label-lg text-label-lg font-bold transition-colors
                          {{ request()->routeIs('dashboard.tickets') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-on-surface-variant hover:text-primary' }}">
                    Mis tickets
                </a>
                <a href="{{ route('dashboard') }}"
                   class="font-label-lg text-label-lg font-bold transition-colors
                          {{ request()->routeIs('dashboard') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-on-surface-variant hover:text-primary' }}">
                    Perfil
                </a>
            </div>
        </div>

        {{-- Derecha: carrito + cerrar sesión --}}
        <div class="hidden md:flex items-center gap-6">
            <button class="material-symbols-outlined text-primary hover:bg-surface-container-low p-2 rounded-full transition-all">
                shopping_cart
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="border border-primary text-primary px-4 py-2 rounded-lg font-label-lg text-label-lg font-bold hover:bg-primary hover:text-on-primary transition-all">
                    Cerrar sesión
                </button>
            </form>
        </div>

        {{-- Hamburguesa mobile --}}
        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-on-surface-variant">
            <span class="material-symbols-outlined">{{ 'menu' }}</span>
        </button>

        @else
        {{-- ── Navbar guest ───────────────────────────────────── --}}

        <a href="{{ route('home') }}" class="font-display text-2xl text-primary tracking-tight">
            Butaca
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('home') }}"
               class="font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors">
                Inicio
            </a>
            <a href="{{ route('catalog') }}"
               class="font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors">
                Cartelera
            </a>
            <a href="{{ route('login') }}"
               class="font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors">
                Iniciar sesión
            </a>
            <a href="{{ route('register') }}"
               class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-lg text-label-lg hover:brightness-110 transition-all">
                Crear cuenta
            </a>
        </div>

        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-on-surface-variant">
            <span class="material-symbols-outlined">{{ 'menu' }}</span>
        </button>

        @endauth
    </div>

    {{-- ── Drawer mobile ──────────────────────────────────────── --}}
    <div x-show="mobileOpen" x-cloak
         class="md:hidden bg-background border-t border-outline-variant/20 px-4 py-3 space-y-1">
        @auth
            <a href="{{ route('catalog') }}"
               class="flex items-center py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors border-b border-outline-variant/10">
                Cartelera
            </a>
            <a href="{{ route('dashboard.tickets') }}"
               class="flex items-center py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors border-b border-outline-variant/10">
                Mis tickets
            </a>
            <a href="{{ route('dashboard') }}"
               class="flex items-center py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors border-b border-outline-variant/10">
                Perfil
            </a>
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="border border-primary text-primary px-4 py-2 rounded-lg font-label-lg text-label-lg font-bold hover:bg-primary hover:text-on-primary transition-all">
                    Cerrar sesión
                </button>
            </form>
        @else
            <a href="{{ route('home') }}"
               class="block py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors">
                Inicio
            </a>
            <a href="{{ route('catalog') }}"
               class="block py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors">
                Cartelera
            </a>
            <a href="{{ route('login') }}"
               class="block py-3 font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors">
                Iniciar sesión
            </a>
            <a href="{{ route('register') }}"
               class="block py-3 font-label-lg text-label-lg text-primary font-bold">
                Crear cuenta
            </a>
        @endauth
    </div>
</nav>
