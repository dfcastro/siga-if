<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-g">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />




     @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-grow">
            <div class="py-5">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>

        </main>
        <footer class="w-full bg-white border-t border-gray-200  mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-center text-center sm:text-left space-y-2 sm:space-y-0">

                    {{-- Seção de Copyright --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        &copy; {{ date('Y') }} SIGA-IF - Sistema Integrado de Gestão de Acesso.
                    </div>

                    {{-- Seção de Créditos de Desenvolvimento --}}
                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        <p>Desenvolvido pelo NTI - Núcleo de Tecnologia da Informação</p>
                        <p>IFNMG - Campus Almenara</p>
                    </div>

                </div>
            </div>
        </footer>
    </div>


    {{-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script> --}}

    @livewireScripts
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>
