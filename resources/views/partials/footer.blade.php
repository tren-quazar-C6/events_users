<footer class="w-full bg-surface-container-low border-t border-secondary-container/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-16 grid grid-cols-1 md:grid-cols-4 gap-6">

        {{-- Brand --}}
        <div class="space-y-6">
            <h2 class="font-display text-headline-md text-primary">Butaca</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Creando puentes entre el escenario y el público desde {{ date('Y') }}.
            </p>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-secondary-container/50 flex items-center justify-center text-primary hover:bg-secondary-container transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 20px">share</span>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-secondary-container/50 flex items-center justify-center text-primary hover:bg-secondary-container transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 20px">public</span>
                </a>
            </div>
        </div>

        {{-- Compañía --}}
        <div class="space-y-6">
            <h4 class="font-label-lg text-label-lg text-on-surface uppercase tracking-wider">Compañía</h4>
            <nav class="flex flex-col gap-4">
                <a href="{{ route('home') }}" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">Inicio</a>
                <a href="{{ route('catalog') }}" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">Cartelera</a>
                <a href="#" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">Prensa</a>
            </nav>
        </div>

        {{-- Ayuda --}}
        <div class="space-y-6">
            <h4 class="font-label-lg text-label-lg text-on-surface uppercase tracking-wider">Ayuda</h4>
            <nav class="flex flex-col gap-4">
                <a href="#" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">Soporte</a>
                <a href="#" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">Términos</a>
                <a href="#" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">Privacidad</a>
                <a href="#" class="font-body-md text-body-md text-on-secondary-container hover:text-primary transition-colors">FAQs</a>
            </nav>
        </div>

        {{-- Contacto --}}
        <div class="space-y-6">
            <h4 class="font-label-lg text-label-lg text-on-surface uppercase tracking-wider">Contacto</h4>
            <p class="font-body-md text-body-md text-on-secondary-container">hola@butaca.theater</p>
            <p class="font-body-md text-body-md text-on-secondary-container">Proyecto Quasar · Colombia</p>
        </div>

    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-8 border-t border-secondary-container/20 text-center">
        <p class="font-label-sm text-label-sm text-on-secondary-container/60 italic">
            © {{ date('Y') }} Butaca Theater. Theater for Everyone.
        </p>
    </div>
</footer>
