@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-black text-cyan-700 bg-gradient-to-r from-cyan-50 to-blue-50 border border-cyan-200 shadow-sm transition duration-150 ease-in-out'
            : 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
