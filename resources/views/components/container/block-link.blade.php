@props([
    'href' => null,
])

<a
    {{ $attributes->merge(['class' => 'sn-cms-container-block-link']) }}
    {{ \Filament\Support\generate_href_html($href) }}
>
    {{ $slot }}
</a>
