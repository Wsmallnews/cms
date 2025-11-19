@props([
    'tag' => 'div',
    'href' => null,
])

<{{ $tag }}
    {{ ($tag == 'a' && $href) ? \Filament\Support\generate_href_html($href) : '' }}
    {{ $attributes->merge(['class' => 'p-4 rounded-md shadow-sm ring-1 bg-white ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10']) }}
>
    {{ $slot }}
</{{ $tag }}>