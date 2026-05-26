<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-3.5 bg-amber-600 border border-transparent rounded-lg font-bold text-white hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-amber-200/50']) }}>
    {{ $slot }}
</button>
