@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 border-l-[3px] border-accent text-start text-sm font-semibold text-accent bg-accent-light/50 rounded-r-lg'
            : 'block w-full ps-3 pe-4 py-2.5 border-l-[3px] border-transparent text-start text-sm font-medium text-neutral-600 hover:text-neutral-800 hover:bg-neutral-50 hover:border-neutral-200 rounded-r-lg';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>