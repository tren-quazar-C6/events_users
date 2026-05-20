<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Tickify</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream font-sans text-sage-dark min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="font-display text-5xl text-sage-dark">Tickify</h1>
            <p class="mt-2 text-sage-dark/70">Bienvenida de vuelta</p>
        </div>

        <div class="bg-white rounded-card shadow-soft p-8">
            <h2 class="text-2xl font-display text-sage-dark mb-6">Iniciar sesión</h2>

            {{-- Errores generales (validación o credenciales malas) --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-btn bg-coral/10 border border-coral">
                    <ul class="text-sm text-coral list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="#" class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-btn border border-sage-dark/20 bg-white hover:bg-cream transition font-medium text-sage-dark">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="shrink-0">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuar con Google
            </a>

            {{-- Divisor "o continúa con email" --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-sage-dark/15"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-sm text-sage-dark/60">o con tu correo</span>
                </div>
            </div>

            <form method="POST" action="{{ route('auth.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold mb-2">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="username"
                           class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold mb-2">Contraseña</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                </div>

                <label class="flex items-center gap-2 text-sm text-sage-dark/80 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-sage-dark/30 text-sage focus:ring-sage" />
                    Recordarme
                </label>

                <button type="submit"
                        class="w-full bg-sage text-white font-semibold py-3 rounded-btn hover:bg-sage-dark transition">
                    Iniciar sesión
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-sage-dark/70">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-sage font-semibold hover:underline">Regístrate</a>
            </p>
        </div>
    </div>

</body>
</html>