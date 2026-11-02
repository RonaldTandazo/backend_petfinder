<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#123d2b">

    <title>@yield('title', 'PetFinder')</title>

    <link rel="stylesheet" href="{{ asset('css/web/main.css') }}">

    @stack('styles')
</head>

<body>

    @include('web.components.header')

    <main class="page-container">
        <div class="page-illustration">
            @yield('illustration', '🐶 🐱')
        </div>

        <section class="page-card">
            @yield('content')
        </section>
    </main>

    @include('web.components.footer')

    @stack('scripts')

</body>
</html>