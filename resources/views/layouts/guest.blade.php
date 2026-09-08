<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center bg-gray-100 px-4 py-8 sm:justify-center sm:px-6 sm:py-10">
            <div>
                <a href="/" class="text-3xl font-extrabold tracking-wider text-indigo-600 sm:text-4xl">
                    CARUNA
                </a>
            </div>

            <div class="mt-6 w-full max-w-md overflow-hidden rounded-2xl bg-white px-5 py-6 shadow-md sm:px-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
