@props(['active'])

@php
$classes = ($active ?? false)
            ? 'sb-nav-link-inner sb-nav-link-inner-active'
            : 'sb-nav-link-inner';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>