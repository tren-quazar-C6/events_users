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
                <a href="#" class="text-sage font-semibold hover:underline">Regístrate</a>
                {{-- href="#" temporal — register lo construimos en el próximo paso --}}
            </p>
        </div>
    </div>

</body>
</html>