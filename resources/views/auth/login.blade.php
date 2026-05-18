<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Butaca</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex items-center justify-center p-6 relative overflow-x-hidden">

    <div class="fixed inset-0 grain-overlay z-0"></div>
    <div class="fixed inset-0 spotlight-glow z-0 pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="font-display text-display text-primary tracking-tight leading-none">Butaca</a>
            <p class="mt-3 font-body-md text-body-md text-on-surface-variant">Bienvenido de vuelta</p>
        </div>

        {{-- Card --}}
        <div class="bg-surface-container-lowest rounded-[24px] shadow-[0_8px_32px_rgba(50,105,78,0.08)] border border-secondary-container/20 p-8">

            {{-- Google OAuth --}}
            <a href="#"
               class="w-full flex items-center justify-center gap-3 px-6 py-3.5 border-2 border-secondary-container/60 rounded-xl font-label-lg text-label-lg text-on-surface hover:bg-surface-container-low hover:border-primary/30 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 shrink-0">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuar con Google
            </a>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-secondary-container/40"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-3 bg-surface-container-lowest font-label-sm text-label-sm text-on-surface-variant">
                        o continúa con tu email
                    </span>
                </div>
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-error-container/40 border border-error/20">
                    <ul class="font-label-sm text-label-sm text-error list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('auth.attempt') }}" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label for="email" class="block font-label-lg text-label-lg text-on-surface">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="username"
                           placeholder="tu@correo.com"
                           class="w-full px-4 py-3 rounded-xl border-2 border-secondary-container/40 bg-surface-container-low font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-all placeholder:text-on-surface-variant/40" />
                </div>

                <div class="space-y-1">
                    <label for="password" class="block font-label-lg text-label-lg text-on-surface">Contraseña</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border-2 border-secondary-container/40 bg-surface-container-low font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:bg-surface-container-lowest transition-all placeholder:text-on-surface-variant/40" />
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember"
                           class="rounded border-secondary-container text-primary focus:ring-primary focus:ring-offset-0" />
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Recordarme</span>
                </label>

                <button type="submit"
                        class="w-full bg-primary text-on-primary font-label-lg text-label-lg py-3.5 rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-md shadow-primary/20 mt-2">
                    Iniciar sesión
                </button>
            </form>

            <p class="mt-6 text-center font-label-sm text-label-sm text-on-surface-variant">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-primary font-bold hover:underline underline-offset-4">
                    Regístrate gratis
                </a>
            </p>
        </div>

    </div>

</body>
</html>
