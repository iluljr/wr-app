@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-4 pe-4 py-2 rounded-md text-base font-medium text-white bg-white/20 focus:outline-none focus:bg-white/25 transition duration-150 ease-in-out'
            : 'block w-full ps-4 pe-4 py-2 rounded-md text-base font-medium text-sky-100/90 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/15 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
