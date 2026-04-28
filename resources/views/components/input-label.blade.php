@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[11px] font-black uppercase tracking-wider text-slate-500']) }}>
    {{ $value ?? $slot }}
</label>
