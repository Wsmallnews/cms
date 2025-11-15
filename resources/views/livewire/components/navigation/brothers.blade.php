<x-dynamic-component :component="$wrapperView" 
    @class([
        'w-full md:w-72 shrink-0',
        'hidden' => $brothers->isEmpty(),
    ])
>
    @if ($brothers->isNotEmpty())
        <ul class="w-full flex flex-col shrink-0 bg-primary-500 divide-y divide-primary-400">
            @foreach ($brothers as $brother)
                @if ($brother->children->count() > 0)
                    <li>
                        <div x-data="{ isExpanded: {{ $brother->has_active ? 'true' : 'false' }} }">
                            <button id="controlsAccordionItem{{$brother->id}}" type="button"
                                @class([
                                    'flex w-full items-center justify-between gap-4 p-4 font-bold text-white hover:bg-primary-600 underline-offset-2 focus-visible:underline focus-visible:outline-none',
                                    'bg-primary-600' => $brother->has_active,
                                ])
                                aria-controls="accordionItem{{$brother->id}}"
                                @click="isExpanded = ! isExpanded"
                                :aria-expanded="isExpanded ? 'true' : 'false'">
                                {{ $brother->name_label }}
                                <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 transform transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
                            </button>
                            <div class="flex flex-col border-t border-primary-400 divide-y divide-primary-400"
                                id="accordionItem{{$brother->id}}"
                                x-cloak x-show="isExpanded"
                                x-collapse
                                role="menu"
                                aria-labelledby="controlsAccordionItemOne{{$brother->id}}"
                            >
                                @foreach ($brother->children as $child)
                                    <a @class([
                                            'flex w-full h-full p-4 font-bold text-white hover:bg-primary-600',
                                            'bg-primary-600' => $child->has_active,
                                        ])
                                        {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                        role="menuitem"
                                    >
                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $child->name_label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                @else 
                    <li @class([
                        'flex hover:bg-primary-600 transition-colors duration-300 ease-in-out',
                        'bg-primary-600' => $brother->has_active,
                    ])>
                        <a class="flex grow p-4 font-bold text-white focus:underline"
                            {{ \Filament\Support\generate_href_html($brother->url_info['url'], $brother->url_info['target'] ?? '_self') }}
                            role="menuitem"
                            aria-current="page"
                        >
                            {{ $brother->name_label }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</x-dynamic-component>