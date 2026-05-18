## siguiente bloque: cartelera
<!DOCTYPE html>

<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Butaca - Teatro para todos</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@500;600;700;800&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "background": "#fcf9f4",
                    "surface": "#fcf9f4",
                    "on-background": "#1c1c19",
                    "on-surface-variant": "#404943",
                    "secondary-container": "#cae6d6",
                    "error": "#ba1a1a",
                    "on-surface": "#1c1c19",
                    "on-primary-container": "#08452e",
                    "on-error": "#ffffff",
                    "surface-container-lowest": "#ffffff",
                    "on-secondary-container": "#4f685b",
                    "tertiary-fixed": "#ffdbc8",
                    "primary": "#32694e",
                    "surface-container-high": "#ebe8e3",
                    "on-primary-fixed-variant": "#175037",
                    "primary-container": "#7bb394",
                    "on-secondary": "#ffffff",
                    "surface-tint": "#32694e",
                    "surface-container-low": "#f6f3ee",
                    "outline": "#707973",
                    "on-tertiary-fixed-variant": "#663d22",
                    "tertiary": "#815437",
                    "surface-dim": "#dcdad5",
                    "secondary-fixed": "#cde9d9",
                    "tertiary-fixed-dim": "#f6ba96",
                    "on-secondary-fixed": "#072016",
                    "surface-container": "#f0ede9",
                    "inverse-primary": "#9ad3b2",
                    "on-secondary-fixed-variant": "#334c40",
                    "on-error-container": "#93000a",
                    "on-tertiary-container": "#5a3319",
                    "surface-container-highest": "#e5e2dd",
                    "inverse-surface": "#31302d",
                    "secondary-fixed-dim": "#b1cdbd",
                    "on-primary": "#ffffff",
                    "primary-fixed-dim": "#9ad3b2",
                    "primary-fixed": "#b5f0ce",
                    "outline-variant": "#c0c9c1",
                    "on-primary-fixed": "#002113",
                    "secondary": "#4b6457",
                    "on-tertiary": "#ffffff",
                    "surface-variant": "#e5e2dd",
                    "surface-bright": "#fcf9f4",
                    "on-tertiary-fixed": "#321300",
                    "inverse-on-surface": "#f3f0eb",
                    "error-container": "#ffdad6",
                    "tertiary-container": "#d39b7a"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "margin-mobile": "16px",
                    "gutter": "24px",
                    "margin-desktop": "64px",
                    "unit": "8px",
                    "container-max": "1280px"
            },
            "fontFamily": {
                    "headline-md": ["Bricolage Grotesque"],
                    "label-lg": ["Plus Jakarta Sans"],
                    "body-lg": ["Plus Jakarta Sans"],
                    "display": ["Bricolage Grotesque"],
                    "label-sm": ["Plus Jakarta Sans"],
                    "headline-lg-mobile": ["Bricolage Grotesque"],
                    "headline-lg": ["Bricolage Grotesque"],
                    "body-md": ["Plus Jakarta Sans"]
            },
            "fontSize": {
                    "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "500"}],
                    "label-lg": ["14px", {"lineHeight": "1.4", "letterSpacing": "0.01em", "fontWeight": "600"}],
                    "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
                    "display": ["48px", {"lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "600"}],
                    "label-sm": ["12px", {"lineHeight": "1.4", "fontWeight": "500"}],
                    "headline-lg-mobile": ["28px", {"lineHeight": "1.2", "fontWeight": "500"}],
                    "headline-lg": ["32px", {"lineHeight": "1.2", "fontWeight": "500"}],
                    "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .grain-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
        .spotlight-glow {
            background: radial-gradient(circle at center, rgba(123, 179, 148, 0.15) 0%, transparent 70%);
        }
        .stage-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-primary-container selection:text-on-primary-container">
<div class="grain-overlay"></div>
<!-- TopAppBar -->
<header class="sticky top-0 w-full z-50 bg-background/95 backdrop-blur-sm shadow-sm shadow-on-surface/5">
<div class="max-w-container-max mx-auto px-margin-desktop flex items-center justify-between h-20">
<div class="flex items-center gap-8">
<a class="font-display text-display text-primary tracking-tight" href="/">Butaca</a>
<nav class="hidden md:flex gap-6">
<a class="text-primary font-bold border-b-2 border-primary pb-1 font-label-lg text-label-lg" href="#">Cartelera</a>
<a class="text-on-surface-variant font-medium hover:text-primary transition-colors font-label-lg text-label-lg" href="#">Mis tickets</a>
<a class="text-on-surface-variant font-medium hover:text-primary transition-colors font-label-lg text-label-lg" href="#">Perfil</a>
</nav>
</div>
<div class="flex items-center gap-4">
<button class="p-2 rounded-full hover:bg-surface-container-low transition-all duration-200 text-on-surface-variant">
<span class="material-symbols-outlined" data-icon="shopping_cart">shopping_cart</span>
</button>
<button class="bg-surface-container-low text-primary px-6 py-2 rounded-full font-label-lg text-label-lg hover:bg-surface-container-high transition-all duration-200">
                    Cerrar sesión
                </button>
</div>
</div>
</header>
<main>
<!-- Hero Section -->
<section class="relative pt-12 pb-24 overflow-hidden">
<div class="absolute inset-0 spotlight-glow -z-10"></div>
<div class="stage-container px-margin-desktop">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<div class="lg:col-span-7 space-y-8">
<div class="inline-flex items-center gap-2 px-4 py-2 bg-secondary-container text-on-secondary-container rounded-full font-label-lg text-label-lg">
<span class="material-symbols-outlined text-[18px]" data-icon="theater_comedy">theater_comedy</span>
                            Teatro para todos
                        </div>
<h1 class="font-display text-[64px] leading-[1.05] tracking-tight text-on-surface">
                            Vive la emoción del <span class="text-primary italic">escenario vivo</span>.
                        </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
                            Butaca acerca la cultura a cada rincón. Encuentra las mejores obras, musicales y clásicos con una experiencia cálida y humana.
                        </p>
<!-- Search Bar -->
<div class="relative max-w-2xl group">
<div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-outline">
<span class="material-symbols-outlined" data-icon="search">search</span>
</div>
<input class="w-full h-16 pl-14 pr-6 bg-surface-container-lowest border-2 border-outline/10 rounded-2xl font-body-md text-body-md focus:ring-0 focus:border-primary transition-all shadow-sm" placeholder="Buscar por obra, teatro o género..." type="text"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 bg-primary text-on-primary px-8 py-3 rounded-xl font-label-lg text-label-lg hover:scale-102 transition-transform active:scale-95">
                                Buscar
                            </button>
</div>
</div>
<div class="lg:col-span-5 relative hidden lg:block">
<div class="aspect-[4/5] rounded-[32px] overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
<img class="w-full h-full object-cover" data-alt="A warm and inviting theatrical scene featuring a close-up of heavy velvet curtains in a soft sage green color, illuminated by a warm spotlight that creates a golden glow. The lighting is soft and atmospheric, reflecting a minimalist yet tactile design aesthetic. The composition is artistic and clean, evoking the human-centric and optimistic philosophy of the theater brand." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWLndKEc8xxuPlKp5ITN8_Kv7l2KeHFFWJO-QqZ7xaVs2nei8VlHgumfCvPyB_BoQpp9bveDT9Qb3yozfRoI8GgddBeHlVDYjBC150W7bnyiwxSiQoOWbvcYXoKCvm5Sg0ptLWAoZb_MClf8wbsrusrJvzf-A2Yu8oNcF18RDNBLqUqFjfl1ppPMJfNZ6745R8ZT_B1eGnHhx3StAwZBO3-aqufsddB_XheL1h_OGuByHxHI32gjWH6RsYWgETAWDLDTVx3bGTXTLz"/>
</div>
<!-- Floating Card Decor -->
<div class="absolute -bottom-6 -left-12 bg-white p-6 rounded-2xl shadow-xl border border-secondary-container/30 flex items-center gap-4 max-w-xs animate-bounce-slow">
<div class="w-12 h-12 rounded-full bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed-variant">
<span class="material-symbols-outlined" data-icon="confirmation_number">confirmation_number</span>
</div>
<div>
<p class="font-label-lg text-label-lg text-on-surface">Próximo estreno</p>
<p class="font-body-md text-body-md text-on-surface-variant">Hamlet en Madrid</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Destacados Section (Bento Grid) -->
<section class="py-24 bg-surface-container-low/50">
<div class="stage-container px-margin-desktop">
<div class="flex justify-between items-end mb-12">
<div class="space-y-2">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Destacados</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Las obras que están marcando tendencia esta temporada.</p>
</div>
<a class="text-primary font-label-lg text-label-lg hover:underline underline-offset-4 flex items-center gap-2" href="#">
                        Ver toda la cartelera
                        <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
<!-- Feature Large -->
<div class="md:col-span-2 md:row-span-2 relative group rounded-[24px] overflow-hidden aspect-square md:aspect-auto">
<img class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" data-alt="A grand theater stage from a low-angle perspective, showing rows of empty seats in a warm cream and sage green color scheme. A single spotlight beams down from above, creating a dramatic yet welcoming atmosphere. The visual style is clean and minimalist, with a tactile paper-like grain overlay that enhances the organic and human feel of the performing arts venue." src="https://lh3.googleusercontent.com/aida-public/AB6AXuByvC7jKiFhihVRAnoQPGoOsNleoCNV7ZZ0rt1eCV0R0XUZeHrJntW5GFbMtQZrrOTvRpYfgxAg7tviG7jR3WeC7vzwyaoog1U3jaup4vV7BtDEV1XtBD8jSlhNKrl95XfLDQa4zPbcr_qx3qzOy8yGM0kILBBTAXhEMWo9ejDdbIW593_zHPaiHV9bN2FMapM4WOPHDKzTZBzaCcue8dLjehCw_p38wmOXOovdQ57WtmFp0TSGH88FE3IXHNxBssmISHR8roeKsF2c"/>
<div class="absolute inset-0 bg-gradient-to-t from-on-background/80 via-on-background/20 to-transparent"></div>
<div class="absolute bottom-0 left-0 p-8 w-full">
<span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full font-label-sm text-label-sm mb-4 inline-block">Más Popular</span>
<h3 class="font-headline-md text-[32px] text-white mb-2">Los Miserables: El Musical</h3>
<p class="text-white/80 font-body-md text-body-md mb-6">Una producción épica que redefine el clásico de Víctor Hugo.</p>
<button class="bg-white text-on-background px-8 py-3 rounded-xl font-label-lg text-label-lg hover:bg-secondary-container transition-colors">Reservar Butaca</button>
</div>
</div>
<!-- Side Grid 1 -->
<div class="bg-surface-container-highest p-6 rounded-[24px] flex flex-col justify-between group hover:shadow-lg transition-all border border-transparent hover:border-primary/10">
<div class="aspect-video rounded-xl overflow-hidden mb-4">
<img class="w-full h-full object-cover" data-alt="A stylized digital illustration of a contemporary dance performance with fluid, organic movements. The lighting uses soft secondary mint and warm peach tones, creating a tranquil and artistic mood. The style is minimalist with subtle paper-like textures, reflecting a fresh and optimistic approach to theatrical arts without traditional elitism." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjLilPFd3a1FU-eOPvuGsefCpxvbe2v8G0xitjf9IU6vdOGwXRpVOdS3yhN6myma9fNmh9m57TUgo6tXWEW2WYkURO4B8TmydYkeE5OIeKcxvZD76j0VqgsyiQFKlVsff3qYhNghmAUWdf7wqeO7ByyZDtAXcqfKlL-Sn2TSAT3EeWiC3GKsiaItD2gSnmfbqSbAtovOPGCy0UBg2guvfyAI1_9D8NsNlRopY--TodJ9BItRRuGMcH3mf9HH7u3NGgNGqWua1mR2pB"/>
</div>
<div>
<span class="text-tertiary font-label-sm text-label-sm uppercase tracking-wider mb-1 block">Danza Moderna</span>
<h4 class="font-headline-md text-headline-md text-on-surface mb-4">Ecos del Silencio</h4>
<div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px]" data-icon="calendar_today">calendar_today</span>
                                12 - 24 Oct
                            </div>
</div>
</div>
<!-- Side Grid 2 -->
<div class="bg-surface-container-highest p-6 rounded-[24px] flex flex-col justify-between group hover:shadow-lg transition-all border border-transparent hover:border-primary/10">
<div class="aspect-video rounded-xl overflow-hidden mb-4">
<img class="w-full h-full object-cover" data-alt="A cozy, intimate theater setting featuring a small stage with a single wooden chair and a vintage microphone. The background is a soft, deep forest green with warm amber lighting accents. The overall atmosphere is inviting and accessible, emphasizing the theater for everyone concept through a minimalist and human-centric visual language." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBCsJJ0NccOwVkQDJ7AIdrsYhAKjrucU4X8O5qr-_LkLdAsBiUa_wzMmucOjSe7YrWSwsUwubga40a9N5DXWs4oUHtbsuDa7T_n12ReLGSpXjn_4lD27eQ1yMfiZmSbWeYFgY1Amhgc_QWiTWEyx7q-cAVmgX_X3cPYfb7sZsESEu_RgkElAFIakXZMhA1YohvILzRRMHjauIaySzJLJZgWMB0wpJla9KPHl-ui3-oUHz_qTG24EMSxOQi7c3nxGZenYlgXu00k60gw"/>
</div>
<div>
<span class="text-tertiary font-label-sm text-label-sm uppercase tracking-wider mb-1 block">Comedia</span>
<h4 class="font-headline-md text-headline-md text-on-surface mb-4">No Hay Drama</h4>
<div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px]" data-icon="location_on">location_on</span>
                                Teatro Cervantes
                            </div>
</div>
</div>
<!-- Side Grid 3 - Full Width on Mobile, Quarter on Desktop -->
<div class="md:col-span-2 bg-secondary-container p-8 rounded-[24px] flex items-center gap-8 group">
<div class="flex-1">
<h4 class="font-headline-md text-[28px] text-on-primary-container mb-2">Descuento Familiar</h4>
<p class="font-body-md text-body-md text-on-secondary-container mb-6">4 entradas al precio de 3 en todas las funciones matinales de los domingos.</p>
<button class="bg-primary text-on-primary px-6 py-2 rounded-lg font-label-lg text-label-lg">Saber más</button>
</div>
<div class="hidden sm:block w-32 h-32 rounded-full bg-on-primary-container/10 flex items-center justify-center">
<span class="material-symbols-outlined text-[64px] text-on-primary-container" data-icon="family_restroom">family_restroom</span>
</div>
</div>
</div>
</div>
</section>
<!-- Próximas Funciones Section (List) -->
<section class="py-24">
<div class="stage-container px-margin-desktop">
<div class="max-w-3xl mx-auto text-center mb-16">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Próximas Funciones</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">Encuentra tu asiento perfecto. Organizamos las funciones por cercanía y disponibilidad.</p>
</div>
<div class="space-y-4">
<!-- Ticket Item 1 -->
<div class="bg-background border border-secondary-container/30 hover:border-primary/30 p-6 rounded-[20px] flex flex-col md:flex-row items-center gap-8 transition-all hover:shadow-md group">
<div class="flex flex-col items-center justify-center bg-surface-container-low w-24 h-24 rounded-2xl border-2 border-primary/5">
<span class="font-display text-[28px] text-primary">15</span>
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">OCT</span>
</div>
<div class="flex-1 text-center md:text-left">
<h5 class="font-headline-md text-headline-md text-on-surface">Bodas de Sangre</h5>
<p class="font-body-md text-body-md text-on-surface-variant">Federico García Lorca • Teatro Español</p>
</div>
<div class="flex flex-wrap gap-2 justify-center">
<span class="px-4 py-1.5 bg-surface-container-highest rounded-full text-on-surface-variant font-label-sm text-label-sm">19:30h</span>
<span class="px-4 py-1.5 bg-surface-container-highest rounded-full text-on-surface-variant font-label-sm text-label-sm">21:00h</span>
</div>
<button class="w-full md:w-auto bg-primary text-on-primary px-8 py-3 rounded-xl font-label-lg text-label-lg group-hover:scale-102 transition-transform">
                            Comprar Tickets
                        </button>
</div>
<!-- Ticket Item 2 -->
<div class="bg-background border border-secondary-container/30 hover:border-primary/30 p-6 rounded-[20px] flex flex-col md:flex-row items-center gap-8 transition-all hover:shadow-md group">
<div class="flex flex-col items-center justify-center bg-surface-container-low w-24 h-24 rounded-2xl border-2 border-primary/5">
<span class="font-display text-[28px] text-primary">18</span>
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">OCT</span>
</div>
<div class="flex-1 text-center md:text-left">
<h5 class="font-headline-md text-headline-md text-on-surface">El Rey León</h5>
<p class="font-body-md text-body-md text-on-surface-variant">Lope de Vega • Gran Vía</p>
</div>
<div class="flex flex-wrap gap-2 justify-center">
<span class="px-4 py-1.5 bg-surface-container-highest rounded-full text-on-surface-variant font-label-sm text-label-sm">20:00h</span>
</div>
<button class="w-full md:w-auto bg-primary text-on-primary px-8 py-3 rounded-xl font-label-lg text-label-lg group-hover:scale-102 transition-transform">
                            Comprar Tickets
                        </button>
</div>
<!-- Ticket Item 3 -->
<div class="bg-background border border-secondary-container/30 hover:border-primary/30 p-6 rounded-[20px] flex flex-col md:flex-row items-center gap-8 transition-all hover:shadow-md group">
<div class="flex flex-col items-center justify-center bg-surface-container-low w-24 h-24 rounded-2xl border-2 border-primary/5">
<span class="font-display text-[28px] text-primary">22</span>
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase">OCT</span>
</div>
<div class="flex-1 text-center md:text-left">
<h5 class="font-headline-md text-headline-md text-on-surface">La Casa de Bernarda Alba</h5>
<p class="font-body-md text-body-md text-on-surface-variant">Teatro María Guerrero</p>
</div>
<div class="flex flex-wrap gap-2 justify-center">
<span class="px-4 py-1.5 bg-surface-container-highest rounded-full text-on-surface-variant font-label-sm text-label-sm">18:00h</span>
<span class="px-4 py-1.5 bg-surface-container-highest rounded-full text-on-surface-variant font-label-sm text-label-sm">20:30h</span>
</div>
<button class="w-full md:w-auto bg-primary text-on-primary px-8 py-3 rounded-xl font-label-lg text-label-lg group-hover:scale-102 transition-transform">
                            Comprar Tickets
                        </button>
</div>
</div>
</div>
</section>
<!-- Newsletter / Invitation -->
<section class="py-24">
<div class="stage-container px-margin-desktop">
<div class="bg-secondary-fixed rounded-[40px] p-12 md:p-20 text-center relative overflow-hidden">
<div class="absolute top-0 left-0 w-64 h-64 bg-primary/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
<div class="absolute bottom-0 right-0 w-96 h-96 bg-primary/5 rounded-full translate-x-1/3 translate-y-1/3"></div>
<div class="relative z-10 max-w-2xl mx-auto space-y-8">
<span class="font-label-lg text-label-lg text-primary uppercase tracking-[0.2em]">Únete a la familia</span>
<h2 class="font-display text-[40px] md:text-[56px] text-on-secondary-fixed leading-tight">¿Quieres ser el primero en entrar a sala?</h2>
<p class="font-body-lg text-body-lg text-on-secondary-fixed-variant">Suscríbete para recibir preventas exclusivas, entrevistas con directores y contenido tras bambalinas.</p>
<form class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
<input class="flex-1 h-14 px-6 bg-surface-container-lowest border-none rounded-xl font-body-md focus:ring-2 focus:ring-primary" placeholder="Tu correo electrónico" type="email"/>
<button class="h-14 px-10 bg-on-secondary-fixed text-white rounded-xl font-label-lg text-label-lg hover:bg-on-secondary-fixed/90 transition-colors" type="submit">
                                Suscribirme
                            </button>
</form>
</div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="w-full mt-margin-desktop bg-surface-container-low border-t border-secondary-container/30">
<div class="max-w-container-max mx-auto px-margin-desktop py-16 grid grid-cols-1 md:grid-cols-4 gap-gutter">
<div class="space-y-6">
<h2 class="font-display text-headline-md text-primary">Butaca</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Creando puentes entre el escenario y el público desde 2024.</p>
<div class="flex gap-4">
<a class="w-10 h-10 rounded-full bg-secondary-container/50 flex items-center justify-center text-primary hover:bg-secondary-container transition-colors" href="#">
<span class="material-symbols-outlined text-[20px]" data-icon="share">share</span>
</a>
<a class="w-10 h-10 rounded-full bg-secondary-container/50 flex items-center justify-center text-primary hover:bg-secondary-container transition-colors" href="#">
<span class="material-symbols-outlined text-[20px]" data-icon="public">public</span>
</a>
</div>
</div>
<div class="space-y-6">
<h4 class="font-label-lg text-label-lg text-on-surface uppercase">Compañía</h4>
<nav class="flex flex-col gap-4">
<a class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors" href="#">Privacidad</a>
<a class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors" href="#">Términos</a>
<a class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors" href="#">Prensa</a>
</nav>
</div>
<div class="space-y-6">
<h4 class="font-label-lg text-label-lg text-on-surface uppercase">Ayuda</h4>
<nav class="flex flex-col gap-4">
<a class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors" href="#">Soporte</a>
<a class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors" href="#">Mapa del sitio</a>
<a class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors" href="#">FAQs</a>
</nav>
</div>
<div class="space-y-6">
<h4 class="font-label-lg text-label-lg text-on-surface uppercase">Contacto</h4>
<p class="font-body-md text-body-md text-on-secondary-container">hola@butaca.theater</p>
<p class="font-body-md text-body-md text-on-secondary-container">C. de la Primavera, 12, 28012 Madrid, España</p>
</div>
</div>
<div class="max-w-container-max mx-auto px-margin-desktop py-8 border-t border-secondary-container/20 text-center">
<p class="font-label-sm text-label-sm text-on-secondary-container/60 italic">© 2024 Butaca Theater. Theater for Everyone.</p>
</div>
</footer>
</body></html>
