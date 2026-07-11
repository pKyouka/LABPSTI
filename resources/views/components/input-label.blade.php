@props(['value'])

<label {{ $attributes->merge(['class' => 'fluent-label']) }}>
    {{ $value ?? $slot }}
</label>