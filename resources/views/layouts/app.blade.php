<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Legal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" x-data="{ sidebarOpen: false }">
        <div class="flex">
            <x-sidebar />
            
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex-1">
                <x-header />

                @if (isset($header))
                    <header class="bg-white shadow px-4 py-4 mb-4">
                        {{ $header }}
                    </header>
                @endif

                <main class="p-4">
                    {{ $slot }}
                </main>
            </div> <!-- end content -->
        </div> <!-- end flex -->
        @stack('scripts')
        <script src="https://unpkg.com/lucide@0.271.0"></script>
        <script>
            // Theme initialization
            document.addEventListener('DOMContentLoaded', () => {
                const THEME_KEY = 'lcms-theme-preference';
                const storedTheme = localStorage.getItem(THEME_KEY);
                if (storedTheme) {
                    document.documentElement.setAttribute('data-theme', storedTheme);
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                    localStorage.setItem(THEME_KEY, 'light');
                }
                
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        </script>
    </body>
</html>






