@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
