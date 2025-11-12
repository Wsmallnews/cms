@if ($brothers->isNotEmpty())
    <x-dynamic-component :component="$wrapperView" class="w-full md:w-72">
        <ul class="w-full flex flex-col shrink-0 bg-primary-500">
            @foreach ($brothers as $brother)
                <li @class([
                    'flex hover:bg-primary-600',
                    'bg-primary-600' => $brother->id == $navigation->id,
                ])>
                    <a class="flex grow px-4 py-4 font-bold text-white focus:underline"
                        {{ \Filament\Support\generate_href_html($brother->url_info['url'], $brother->url_info['target'] ?? '_self') }}
                        aria-current="page"
                    >
                        {{ $brother->name_label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-dynamic-component>
@endif