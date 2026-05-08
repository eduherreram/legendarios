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
        <div class="relative lm-guest-bg min-h-screen flex flex-col sm:justify-center items-center px-4 py-6 sm:py-10">
            <div class="relative w-full max-w-md">
                <div class="flex justify-center">
                    <a href="/" class="inline-flex">
                        <img src="{{ asset('images/logo-blanco.png') }}" alt="Legendarios" class="h-16 w-auto drop-shadow-sm sm:h-20" />
                    </a>
                </div>

                <div class="mt-4 rounded-2xl bg-white/90 backdrop-blur shadow-2xl ring-1 ring-black/5 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <x-flash-messages />
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
