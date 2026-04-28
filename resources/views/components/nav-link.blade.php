@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 rounded-xl border border-cyan-200 bg-cyan-50 text-sm font-black leading-5 text-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-4 py-2 rounded-xl border border-transparent text-sm font-bold leading-5 text-slate-600 hover:text-cyan-700 hover:bg-cyan-50 focus:outline-none focus:text-cyan-700 focus:bg-cyan-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
