<x-dynamic-component :component="$wrapperView" 
    @class([
        'w-full md:w-72 shrink-0',
        'hidden' => $brothers->isEmpty(),
    ])
>
    @if ($brothers->isNotEmpty())
        <ul class="w-full flex flex-col shrink-0 bg-primary-500 divide-y divide-primary-400">
            @foreach ($brothers as $brother)
                @php
                    $hasChild = $brother->children->count() > 0;
                @endphp

                <li @class([
                        'w-full flex flex-col items-center justify-between',
                    ])
                    @if ($hasChild)
                        x-data="{ isExpanded: {{ $brother->has_active ? 'true' : 'false' }} }"
                        aria-controls="accordionItem{{$brother->id}}"
                        :aria-expanded="isExpanded ? 'true' : 'false'"
                        aria-haspopup="true"
                    @endif
                    role="menuitem"
                >
                    <a @class([
                            'flex w-full h-14 justify-between items-center px-2 font-bold text-white gap-2',
                            'bg-primary-600' => $brother->has_active,
                        ])
                        @if ($hasChild)
                            @click="isExpanded = ! isExpanded"
                            href="javascript:;"
                        @else
                            {{ \Filament\Support\generate_href_html($brother->url_info['url'], $brother->url_info['target'] ?? false) }}
                        @endif
                    >
                        {{ $brother->name_label }}
                        @if ($hasChild)
                            <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
                        @endif
                    </a>

                    @if ($hasChild) 
                        <ul class="w-full flex flex-col border-t border-primary-400 divide-y divide-primary-400"
                            id="accordionItem{{$brother->id}}"
                            x-cloak x-show="isExpanded"
                            aria-labelledby="controlsAccordionItemOne{{$brother->id}}"
                            x-collapse
                            role="menu"
                        >
                            @foreach ($brother->children as $child)
                                <li @class([
                                        'w-full flex flex-col items-center justify-between',
                                    ])
                                    role="menuitem"
                                >
                                    <a @class([
                                            'flex w-full h-14 justify-between items-center pl-8 pr-2 font-bold text-white gap-2',
                                            'bg-primary-600' => $child->has_active,
                                        ])

                                        {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                    >
                                        {{ $child->name_label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif 
                </li>
            @endforeach
        </ul>
    @endif
</x-dynamic-component>