<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Портал для зоологов')</title>
        @vite('resources/js/app.js')
    </head>
    <body>
        @include('layouts.partials.header')

        <main class="container">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>

        @include('layouts.partials.footer')

        @if ($showCatalogActions ?? false)
            @include('layouts.partials.upload-toast')
        @endif
    </body>
</html>
