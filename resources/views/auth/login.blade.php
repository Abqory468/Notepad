<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
        <p class="text-gray-500 text-sm mt-2">Please enter your details to sign in.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full rounded-lg bg-gray-50 border-gray-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500 text-gray-700 px-4 py-3 transition-colors" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full rounded-lg bg-gray-50 border-gray-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500 text-gray-700 px-4 py-3 transition-colors" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded text-amber-600 border-gray-300 focus:ring-amber-500 cursor-pointer" name="remember">
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-amber-600 hover:text-amber-700" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full px-6 py-3.5 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition shadow-lg shadow-amber-200/50">
                Sign In
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-amber-600 hover:text-amber-700">Sign up</a>
            </p>
        </div>
    </form>
</x-guest-layout>
