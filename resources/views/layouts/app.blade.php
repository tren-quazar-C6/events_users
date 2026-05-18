<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA --}}
    <meta name="theme-color" content="#32694e">
    <link rel="manifest" href="/manifest.json">

    <title>@yield('title', 'Tickify') — Tickify</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:wght@500;600;700&family=Bricolage+Grotesque:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-cream font-sans text-sage-dark min-h-screen flex flex-col">

    <div class="fixed inset-0 grain-overlay z-0"></div>

    @include('partials.navbar')

    <main class="relative flex-1 z-10">
        @yield('content')
    </main>

    @include('partials.footer')

    @livewireScripts

    @stack('scripts')
</body>
</html>
