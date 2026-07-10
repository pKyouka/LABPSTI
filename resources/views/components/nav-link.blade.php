@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-blue-600 shadow-lg shadow-cyan-200/40 ring-1 ring-cyan-400/20 transition-all duration-200'
            : 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/80 hover:shadow-sm transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes . ' sb-nav-link-inner']) }}>
    {{ $slot }}
</a>
