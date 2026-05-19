@extends('layouts.app')

@section('title', 'Cartelera')

@section('content')

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<section class="relative pt-12 pb-24 overflow-hidden">
    <div class="absolute inset-0 spotlight-glow -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">

            {{-- Columna texto --}}
            <div class="lg:col-span-7 space-y-8">

                <div class="inline-flex items-center gap-2 px-4 py-2 bg-secondary-container text-on-secondary-container rounded-full font-label-lg text-label-lg">
                    <span class="material-symbols-outlined" style="font-size: 18px">theater_comedy</span>
                    Teatro para todos
                </div>

                <h1 class="font-display text-[64px] leading-[1.05] tracking-tight text-on-surface">
                    Vive la emoción del <span class="text-primary italic">escenario vivo</span>.
                </h1>

                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
                    Butaca acerca la cultura a cada rincón. Encuentra las mejores obras, musicales y clásicos con una experiencia cálida y humana.
                </p>

                {{-- Buscador --}}
                <div class="relative max-w-2xl">
                    <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-outline">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar por obra, teatro o género..."
                        class="w-full h-16 pl-14 pr-36 bg-surface-container-lowest border-2 border-outline/10 rounded-2xl font-body-md text-body-md focus:ring-0 focus:border-primary transition-all shadow-sm"
                    />
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 bg-primary text-on-primary px-6 py-3 rounded-xl font-label-lg text-label-lg hover:brightness-110 transition-all active:scale-95">
                        Buscar
                    </button>
                </div>
            </div>

            {{-- Columna imagen --}}
            <div class="lg:col-span-5 relative hidden lg:block">
                <div class="aspect-[4/5] rounded-[32px] overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500 bg-secondary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary/20" style="font-size: 120px">curtains</span>
                </div>

                {{-- Tarjeta flotante --}}
                <div class="absolute -bottom-6 -left-12 bg-surface-container-lowest p-6 rounded-2xl shadow-xl border border-secondary-container/30 flex items-center gap-4 max-w-xs">
                    <div class="w-12 h-12 rounded-full bg-tertiary-fixed flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-on-tertiary-fixed-variant">confirmation_number</span>
                    </div>
                    <div>
                        <p class="font-label-lg text-label-lg text-on-surface">Próximo estreno</p>
                        <p class="font-body-md text-body-md text-on-surface-variant">Los Miserables · Jun</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── DESTACADOS — bento grid ─────────────────────────────────── --}}
<section class="py-24 bg-surface-container-low/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16">

        <div class="flex justify-between items-end mb-12">
            <div class="space-y-2">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Destacados</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Las obras que están marcando tendencia esta temporada.</p>
            </div>
            <a href="#proximas" class="text-primary font-label-lg text-label-lg font-bold hover:underline underline-offset-4 flex items-center gap-2 transition-colors">
                Ver toda la cartelera
                <span class="material-symbols-outlined" style="font-size: 18px">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            {{-- Tarjeta feature principal --}}
            <div class="md:col-span-2 md:row-span-2 relative group rounded-[24px] overflow-hidden aspect-square md:aspect-auto min-h-[400px]">
                <div class="absolute inset-0 bg-gradient-to-br from-on-surface via-primary to-secondary-container group-hover:scale-105 transition-transform duration-700 w-full h-full"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-on-background/80 via-on-background/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8 w-full">
                    <span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full font-label-sm text-label-sm mb-4 inline-block">
                        {{ $events['featured']['hero']['badge'] }}
                    </span>
                    <h3 class="font-headline-md text-[32px] text-white mb-2">
                        {{ $events['featured']['hero']['title'] }}
                    </h3>
                    <p class="text-white/80 font-body-md text-body-md mb-6">
                        {{ $events['featured']['hero']['description'] }}
                    </p>
                    <button class="bg-white text-on-background px-8 py-3 rounded-xl font-label-lg text-label-lg hover:bg-secondary-container transition-colors">
                        Reservar Butaca
                    </button>
                </div>
            </div>

            {{-- Tarjetas laterales --}}
            @foreach ($events['featured']['grid'] as $item)
            <div class="bg-surface-container-highest p-6 rounded-[24px] flex flex-col justify-between group hover:shadow-lg transition-all border border-transparent hover:border-primary/10">
                <div class="aspect-video rounded-xl overflow-hidden mb-4 bg-secondary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary/30" style="font-size: 48px">theater_comedy</span>
                </div>
                <div>
                    <span class="text-tertiary font-label-sm text-label-sm uppercase tracking-wider mb-1 block">
                        {{ $item['category'] }}
                    </span>
                    <h4 class="font-headline-md text-headline-md text-on-surface mb-4">
                        {{ $item['title'] }}
                    </h4>
                    <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
                        <span class="material-symbols-outlined" style="font-size: 16px">{{ $item['meta_icon'] }}</span>
                        {{ $item['meta'] }}
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Tarjeta promo --}}
            <div class="md:col-span-2 bg-secondary-container p-8 rounded-[24px] flex items-center gap-8">
                <div class="flex-1">
                    <h4 class="font-headline-md text-[28px] text-on-primary-container mb-2">Descuento Familiar</h4>
                    <p class="font-body-md text-body-md text-on-secondary-container mb-6">
                        4 entradas al precio de 3 en todas las funciones matinales de los domingos.
                    </p>
                    <button class="bg-primary text-on-primary px-6 py-2 rounded-lg font-label-lg text-label-lg hover:brightness-110 transition-all">
                        Saber más
                    </button>
                </div>
                <div class="hidden sm:flex w-32 h-32 rounded-full bg-on-primary-container/10 items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-primary-container" style="font-size: 64px">family_restroom</span>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── PRÓXIMAS FUNCIONES — lista ──────────────────────────────── --}}
<section id="proximas" class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16">

        <div class="max-w-3xl mx-auto text-center mb-16">
            <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Próximas Funciones</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant">
                Encuentra tu asiento perfecto. Organizamos las funciones por cercanía y disponibilidad.
            </p>
        </div>

        <div class="space-y-4">
            @foreach ($events['upcoming'] as $event)
            <div class="bg-background border border-secondary-container/30 hover:border-primary/30 p-6 rounded-[20px] flex flex-col md:flex-row items-center gap-8 transition-all hover:shadow-md group">

                {{-- Fecha --}}
                <div class="flex flex-col items-center justify-center bg-surface-container-low w-24 h-24 rounded-2xl border-2 border-primary/5 shrink-0">
                    <span class="font-display text-[28px] text-primary leading-none">{{ $event['day'] }}</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase mt-1">{{ $event['month'] }}</span>
                </div>

                {{-- Título y venue --}}
                <div class="flex-1 text-center md:text-left">
                    <h5 class="font-headline-md text-headline-md text-on-surface">{{ $event['title'] }}</h5>
                    <p class="font-body-md text-body-md text-on-surface-variant">{{ $event['subtitle'] }}</p>
                </div>

                {{-- Horarios --}}
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach ($event['times'] as $time)
                    <span class="px-4 py-1.5 bg-surface-container-highest rounded-full text-on-surface-variant font-label-sm text-label-sm">
                        {{ $time }}
                    </span>
                    @endforeach
                </div>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                    <a href="{{ route('events.show', $event['id']) }}"
                       class="text-center border border-primary text-primary px-6 py-3 rounded-xl font-label-lg text-label-lg font-bold hover:bg-secondary-container transition-all">
                        Ver info
                    </a>
                    <a href="{{ route('events.seats', $event['id']) }}"
                       class="text-center bg-primary text-on-primary px-8 py-3 rounded-xl font-label-lg text-label-lg group-hover:brightness-110 transition-all">
                        Comprar Tickets
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ── NEWSLETTER ───────────────────────────────────────────────── --}}
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16">
        <div class="bg-secondary-fixed rounded-[40px] p-12 md:p-20 text-center relative overflow-hidden">

            {{-- Decoración de fondo --}}
            <div class="absolute top-0 left-0 w-64 h-64 bg-primary/5 rounded-full -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary/5 rounded-full translate-x-1/3 translate-y-1/3 pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl mx-auto space-y-8">
                <span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.2em]">Únete a la familia</span>

                <h2 class="font-display text-[40px] md:text-[56px] text-on-secondary-fixed leading-tight">
                    ¿Quieres ser el primero en entrar a sala?
                </h2>

                <p class="font-body-lg text-body-lg text-on-secondary-fixed-variant">
                    Suscríbete para recibir preventas exclusivas, entrevistas con directores y contenido tras bambalinas.
                </p>

                <form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                    <input
                        type="email"
                        placeholder="Tu correo electrónico"
                        class="flex-1 h-14 px-6 bg-surface-container-lowest border-none rounded-xl font-body-md text-body-md focus:ring-2 focus:ring-primary"
                    />
                    <button type="submit" class="h-14 px-10 bg-on-secondary-fixed text-white rounded-xl font-label-lg text-label-lg hover:bg-on-secondary-fixed/90 transition-colors">
                        Suscribirme
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

@endsection
