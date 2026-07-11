@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm font-medium text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg mb-4']) }}>
        {{ $status }}
    </div>
@endif