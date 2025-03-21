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

    <style>
        [x-cloak] {
            display: none;
        }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
</head>

<body class="flex flex-col min-h-screen font-sans antialiased bg-white dark:bg-zinc-950">
    <div class="flex">
        <!-- Sidebar -->
        <div class="z-40 h-screen lg:fixed lg:w-64">
            <livewire:layout.user.dashboard-sidebar />
        </div>

        <div class="flex-1 lg:ml-64">
            <!-- Secondary Navigation -->
            <div class="sticky top-0 z-20">
                <livewire:layout.user.dashboard-secondary-navigation />
            </div>

            <!-- Primary Navigation -->
            <div class="lg:sticky lg:top-[0rem] z-50">
                <livewire:layout.user.dashboard-primary-navigation />
            </div>

            <!-- Main Content -->
            <main class="self-stretch flex-1 p-6 space-y-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts
</body>

</html>
