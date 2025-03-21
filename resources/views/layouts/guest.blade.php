<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Practizly') }}</title>

    <!-- Fonts -->
    <link rel="icon" href="{{ asset('practizly-logo-white.svg') }}" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
</head>

<body class="font-sans antialiased">
    <div class="flex flex-col items-center min-h-screen py-6 bg-white sm:justify-center sm:pt-0 dark:bg-zinc-950 px-6">
        <div class="mt-6">
            <a href="/" wire:navigate>
                <x-application-logo />
            </a>
        </div>

        <div class="w-full mt-6 overflow-hidden sm:max-w-sm rounded-xl p-2">
            {{ $slot }}
        </div>
    </div>

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts
</body>

</html>
