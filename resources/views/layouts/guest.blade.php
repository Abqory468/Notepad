<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyNotes') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .soft-shadow { box-shadow: 0 20px 40px -15px rgba(0,0,0,0.08); }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#FFFBF5] min-h-screen flex items-center justify-center p-4 sm:p-8">
        
        <div class="w-full max-w-md bg-white rounded-xl soft-shadow p-8 sm:p-10 relative overflow-hidden border border-gray-200">
            
            <!-- Decorative circle -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-amber-50 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-yellow-50 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex flex-col items-center mb-8">
                <a href="/" class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-lg bg-amber-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5">
                            <path d="M12.613 1.258a1.535 1.535 0 0 1 2.13 2.129l-1.905 2.856a8 8 0 0 1-3.56 2.939 4.011 4.011 0 0 0-2.46-2.46 8 8 0 0 1 2.94-3.56l2.855-1.904ZM5.5 8A2.5 2.5 0 0 0 3 10.5a.5.5 0 0 1-.7.459.75.75 0 0 0-.983 1A3.5 3.5 0 0 0 8 10.5 2.5 2.5 0 0 0 5.5 8Z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-gray-800 tracking-tight">MyNotes</span>
                </a>
            </div>

            <div class="relative z-10">
                {{ $slot }}
            </div>
        </div>

    </body>
</html>
