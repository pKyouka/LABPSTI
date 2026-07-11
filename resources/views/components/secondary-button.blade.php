@props(['type' => 'button', 'size' => 'md'])

@php
$sizeClass = match($size) {
    'sm' => 'fluent-btn-sm',
    'lg' => 'fluent-btn-lg',
    default => '',
};
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => 'fluent-btn fluent-btn-secondary ' . $sizeClass]) }}>
    {{ $slot }}
</button>