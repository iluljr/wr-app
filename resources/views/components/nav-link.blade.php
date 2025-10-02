@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 rounded-full text-sm font-medium text-white bg-white/25 shadow-sm backdrop-blur focus:outline-none focus:ring-2 focus:ring-white/70 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 rounded-full text-sm font-medium text-sky-100/90 hover:text-white hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/40 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
