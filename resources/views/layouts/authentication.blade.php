<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles        

        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
    </head>
    <body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400">

        <main class="bg-white dark:bg-gray-900">

            <div class="relative flex">

                <!-- Content -->
                <div class="w-full md:w-1/2">

                    <div class="min-h-[100dvh] h-full flex flex-col after:flex-1">

                        <!-- Header -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                                <!-- Logo -->
                                <a class="block" href="{{ route('dashboard') }}">
                                    <img src="{{ asset('images/emblema-trompillo.svg') }}" alt="Aerolínea El Trompillo" class="h-12 w-auto" />
                                </a>
                            </div>
                        </div>

                        <div class="max-w-sm mx-auto w-full px-4 py-8">
                            {{ $slot }}
                        </div>

                    </div>

                </div>

                <!-- Image -->
                <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2">
                    <img class="object-cover object-center w-full h-full" src="{{ asset('images/voyage-santa-cruz-bolivie-1.jpg') }}" width="900" height="1200" alt="Catedral de Santa Cruz de la Sierra" />
                    <!-- Velo verde de marca -->
                    <div class="absolute inset-0 bg-linear-to-t from-emerald-950/85 via-emerald-900/20 to-emerald-900/5"></div>
                    <!-- Texto sobre la imagen -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <div class="text-2xl font-bold leading-tight">Descubre Bolivia desde el aire</div>
                        <div class="text-sm text-emerald-100/90 mt-1">Aerolínea El Trompillo · vuelos regionales y charter</div>
                    </div>
                </div>

            </div>

        </main> 

        @livewireScriptConfig
    </body>
</html>
