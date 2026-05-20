<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Create an Account</h2>
        <p class="text-gray-500 text-sm mt-2">Join Notepad-X today and start organizing your thoughts.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                class="w-full rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-blue-500 text-gray-700 px-4 py-3 transition-colors" placeholder="John Doe">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                class="w-full rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-blue-500 text-gray-700 px-4 py-3 transition-colors" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-blue-500 text-gray-700 px-4 py-3 transition-colors" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-blue-500 text-gray-700 px-4 py-3 transition-colors" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full px-6 py-3.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                Register
            </button>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Already registered? 
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Sign in</a>
            </p>
        </div>
    </form>
</x-guest-layout>
