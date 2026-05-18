<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Tickify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream font-sans text-sage-dark p-10 min-h-screen">

    <div class="max-w-2xl mx-auto bg-white rounded-card shadow-soft p-8">
        <h1 class="font-display text-4xl text-sage-dark">
            ¡Hola, {{ auth()->user()->name }}!
        </h1>
        <p class="mt-2 text-sage-dark/70">Sesión iniciada correctamente.</p>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit"
                    class="bg-sage text-white px-4 py-2 rounded-btn font-semibold hover:bg-sage-dark transition">
                Cerrar sesión
            </button>
        </form>
    </div>

</body>
</html>