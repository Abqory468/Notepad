<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyNotes</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .soft-shadow { box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-[#FFFBF5] min-h-screen flex flex-col">

    <div class="w-full flex-1 flex flex-col relative overflow-hidden">
        
        <!-- Navbar -->
        <nav class="flex items-center justify-between px-10 py-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-amber-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-amber-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                        <path d="M12.613 1.258a1.535 1.535 0 0 1 2.13 2.129l-1.905 2.856a8 8 0 0 1-3.56 2.939 4.011 4.011 0 0 0-2.46-2.46 8 8 0 0 1 2.94-3.56l2.855-1.904ZM5.5 8A2.5 2.5 0 0 0 3 10.5a.5.5 0 0 1-.7.459.75.75 0 0 0-.983 1A3.5 3.5 0 0 0 8 10.5 2.5 2.5 0 0 0 5.5 8Z" />
                    </svg>
                </div>
                <span class="text-xl font-extrabold text-gray-800 tracking-tight">MyNotes</span>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('notes.index') }}" class="px-6 py-2.5 bg-white text-amber-700 font-semibold rounded-lg hover:bg-gray-50 transition shadow-sm border border-gray-200">Go to App</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-500 font-medium hover:text-amber-700 transition px-4">Login</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 transition shadow-lg shadow-amber-200/50">Get Started</a>
                @endauth
            </div>
        </nav>

        <!-- Hero -->
        <div class="flex-1 flex flex-col items-center justify-center px-4 relative z-10">
            <div class="max-w-3xl text-center">
                <div class="inline-block px-4 py-1.5 bg-amber-50 text-amber-700 font-semibold rounded-md text-sm mb-8 border border-amber-200">
                    ✨ Your new digital workspace
                </div>
                <h1 class="text-6xl md:text-7xl font-extrabold text-gray-900 mb-8 tracking-tight leading-tight">
                    Write it down.<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-yellow-500">Make it happen.</span>
                </h1>
                <p class="text-xl text-gray-500 mb-12 max-w-2xl mx-auto leading-relaxed">
                    MyNotes is the beautifully simple way to capture your thoughts, organize your ideas, and bring your creativity to life.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    @auth
                        <a href="{{ route('notes.index') }}" class="px-10 py-4 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition shadow-xl shadow-amber-200/50 w-full sm:w-auto text-lg">
                            Open Dashboard →
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-10 py-4 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition shadow-xl shadow-amber-200/50 w-full sm:w-auto text-lg">
                            Start Writing for Free
                        </a>
                        <a href="{{ route('login') }}" class="px-10 py-4 bg-white text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition shadow-sm border border-gray-200 w-full sm:w-auto text-lg">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-1/4 left-10 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-10 w-80 h-80 bg-yellow-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Mini preview mockups floating -->
        <div class="absolute bottom-12 left-12 w-64 bg-white p-6 rounded-xl shadow-xl transform -rotate-6 hidden lg:block opacity-80 pointer-events-none border border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">Meeting Notes</h3>
            <div class="space-y-2">
                <div class="h-2 bg-gray-100 rounded w-full"></div>
                <div class="h-2 bg-gray-100 rounded w-4/5"></div>
            </div>
        </div>

        <div class="absolute top-32 right-12 w-56 bg-white p-6 rounded-xl shadow-xl transform rotate-3 hidden lg:block opacity-80 pointer-events-none border border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">Big Ideas</h3>
            <div class="space-y-2">
                <div class="h-2 bg-gray-100 rounded w-full"></div>
                <div class="h-2 bg-gray-100 rounded w-2/3"></div>
            </div>
        </div>

    </div>

</body>
</html>