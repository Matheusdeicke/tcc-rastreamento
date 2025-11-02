@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' =>
        'inline-flex items-center justify-center px-4 py-2 rounded-xl
         bg-brand-accent text-brand-900 font-semibold tracking-wide
         hover:bg-brand-accentLight focus:outline-none
         focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent
         focus:ring-offset-white transition'
    ]) }}
>
    {{ $slot }}
</button>
