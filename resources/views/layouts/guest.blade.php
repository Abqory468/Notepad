<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Notepad-X') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .soft-shadow { box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05); }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#FAFBFF] min-h-screen flex items-center justify-center p-4 sm:p-8">
        
        <div class="w-full max-w-md bg-white rounded-[2rem] soft-shadow p-8 sm:p-10 relative overflow-hidden border border-gray-100">
            
            <!-- Decorative circle -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-50 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-50 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex flex-col items-center mb-8">
                <a href="/" class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-200">N</div>
                    <span class="text-2xl font-extrabold text-gray-800 tracking-tight">Notepad-X</span>
                </a>
            </div>

            <div class="relative z-10">
                {{ $slot }}
            </div>
        </div>

    </body>
</html>
