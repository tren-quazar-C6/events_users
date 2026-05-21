<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA básico --}}
    <meta name="theme-color" content="#7BB394">
    <link rel="manifest" href="/manifest.json">

    <title>@yield('title', 'Tickify') — Tickify</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-cream font-sans text-sage-dark min-h-screen flex flex-col">

    @include('partials.navbar')

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- @include('partials.footer')

    @stack('scripts') --}}
    
    @include('partials.footer')

        {{-- Bottom navigation para móvil (PWA) --}}
        @include('partials.bottom-nav')

        @stack('scripts')
    </body>
</html>