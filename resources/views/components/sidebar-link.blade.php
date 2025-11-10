@props(['href', 'active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center space-x-3 p-3 rounded-lg text-white font-semibold bg-gray-700 transition duration-150 ease-in-out'
            : 'flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>
