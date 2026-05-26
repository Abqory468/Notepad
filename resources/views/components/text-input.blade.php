@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-50 border-gray-200 focus:bg-white focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-gray-700 px-4 py-2.5 transition-colors']) }}>
