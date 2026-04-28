<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-[0.2em] hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 shadow-lg shadow-blue-100']) }}>
    {{ $slot }}
</button>
