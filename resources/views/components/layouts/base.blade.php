<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link
        href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap"
        rel="stylesheet">
    <script
        defer
        src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    <link
        rel="stylesheet"
        type="text/css"
        href="https://unpkg.com/trix@1.2.3/dist/trix.css">
</head>
<body class="">
{{ $slot }}

@livewireScripts
<script src="https://unpkg.com/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<script src="https://unpkg.com/trix@1.2.3/dist/trix.js"></script>
</body>
</html>
