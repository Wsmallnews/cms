@if ($brothers->isNotEmpty())
    <x-dynamic-component :component="$wrapperView" class="w-full">
        <ul class="flex flex-col w-full md:w-72 shrink-0 bg-primary-500">
            @foreach ($brothers as $brother)
                <li class="flex">
                    <a class="flex grow px-4 py-4 font-bold text-white focus:underline"
                        {{ \Filament\Support\generate_href_html($brother->url_info['url'], $brother->url_info['target'] ?? '_self') }}
                        aria-current="page"
                    >
                        {{ $brother->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-dynamic-component>
@endif