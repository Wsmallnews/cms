@php
    $navigations = $this->getNavigations();
@endphp

<nav class="w-full bg-primary-500" x-data="{ mobileMenuIsOpen: false }" @click.away="mobileMenuIsOpen = false">
    <div class="container hidden md:flex h-16 mx-auto px-4 sm:px-0">
        <ul class="flex h-full" role="menu">
            @foreach ($navigations as $navigation)
                @php
                    $hasChild = $navigation->children->count() > 0;
                @endphp
                <li @class([
                        'min-w-32 flex items-center relative group/child hover:bg-primary-600 transition-colors duration-300 ease-in-out',
                        'bg-primary-600' => $navigation->has_active,
                    ])
                    @if ($hasChild)
                        x-data="{ isOpen: false, openedWithKeyboard: false, leaveTimeout: null }"
                        x-on:mouseover="isOpen = true; leaveTimeout ? clearTimeout(leaveTimeout) : true"
                        x-on:mouseleave.prevent="leaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                        x-on:keydown.esc.prevent="isOpen = false, openedWithKeyboard = false"
                        x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                    @endif
                    role="menuitem"
                >
                    <a class="flex w-full h-full justify-center items-center px-2 font-bold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                        @if ($hasChild)
                            href="javascript:;"
                            x-on:keydown.space.prevent="openedWithKeyboard = true"
                            x-on:keydown.enter.prevent="openedWithKeyboard = true"
                            x-on:keydown.down.prevent="openedWithKeyboard = true"
                            x-bind:aria-expanded="isOpen || openedWithKeyboard"
                            aria-haspopup="true"
                        @else
                            {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                        @endif
                    >
                        {{ $navigation->name_label }}
                        @if ($hasChild)
                            <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300 rotate-0 group-hover/child:rotate-180" aria-hidden="true" />
                        @endif
                    </a>

                    @if ($hasChild) 
                        <div class="w-full absolute top-full left-0 bg-primary-500 z-10"
                            x-cloak x-show="isOpen || openedWithKeyboard"
                            x-transition
                            x-trap="openedWithKeyboard"
                        >
                            <ul class="flex flex-col" role="menu">
                                @foreach ($navigation->children as $child)
                                    @php
                                        $hasGrandChild = $child->children->count() > 0;
                                    @endphp
                                    <li @class([
                                            'w-full h-14 flex items-center relative group/grandchild hover:bg-primary-600 transition-colors duration-300 ease-in-out',
                                            'bg-primary-600' => $child->has_active,
                                        ])
                                        @if ($hasGrandChild)
                                            x-data="{ isOpen: false, openedWithKeyboard: false, leaveTimeout: null }"
                                            x-on:mouseover="isOpen = true; leaveTimeout ? clearTimeout(leaveTimeout) : true"
                                            x-on:mouseleave.prevent="leaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                                            x-on:keydown.esc.prevent="isOpen = false, openedWithKeyboard = false"
                                            x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                                        @endif
                                        role="menuitem"
                                    >
                                        <a class="flex w-full h-full justify-between items-center px-2 font-bold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                                            @if ($hasGrandChild)
                                                href="javascript:;"
                                                x-on:keydown.space.prevent="openedWithKeyboard = true"
                                                x-on:keydown.enter.prevent="openedWithKeyboard = true"
                                                x-on:keydown.down.prevent="openedWithKeyboard = true"
                                                x-bind:aria-expanded="isOpen || openedWithKeyboard"
                                                aria-haspopup="true"
                                            @else
                                                {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                            @endif
                                        >
                                            {{ $child->name_label }}
                                            @if ($hasGrandChild)
                                                <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300 rotate-0 group-hover/child:-rotate-90" aria-hidden="true" />
                                            @endif
                                        </a>

                                        @if ($hasGrandChild) 
                                            <div class="w-full absolute top-0 left-full bg-primary-500"
                                                x-cloak x-show="isOpen || openedWithKeyboard"
                                                x-transition
                                                x-trap="openedWithKeyboard"
                                            >
                                                <ul class="flex flex-col" role="menu">
                                                    @foreach ($child->children as $grandChild)
                                                        <li @class([
                                                                'w-full h-12 flex items-center hover:bg-primary-600 transition-colors duration-300 ease-in-out',
                                                                'bg-primary-600' => $grandChild->has_active,
                                                            ])
                                                            role="menuitem"
                                                        >
                                                            <a class="flex w-full h-full justify-between items-center px-2 font-bold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                                                                
                                                                {{ \Filament\Support\generate_href_html($grandChild->url_info['url'], $grandChild->url_info['target'] ?? false) }}
                                                            >
                                                                {{ $grandChild->name_label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Mobile Menu Button -->
    <button class="flex text-white fixed md:hidden"
        @click="mobileMenuIsOpen = !mobileMenuIsOpen"
        :aria-expanded="mobileMenuIsOpen"
        x-bind:class="mobileMenuIsOpen ? 'fixed top-6 right-4 z-20' : 'absolute top-6 right-4 z-20'"
        type="button"
        aria-label="mobile menu"
        aria-controls="mobileMenu"
    >
        <x-filament::icon icon="heroicon-m-bars-3" class="size-6" x-cloak x-show="!mobileMenuIsOpen" aria-hidden="true" />
        <x-filament::icon icon="heroicon-m-x-mark" class="size-6" x-cloak x-show="mobileMenuIsOpen" aria-hidden="true" />
    </button>

    <!-- Mobile Menu -->
    <ul class="flex flex-col fixed max-h-svh overflow-y-auto divide-y divide-primary-400 inset-x-0 top-0 z-10 rounded-b-md bg-primary-500 pb-6 pt-20 md:hidden"
        x-cloak x-show="mobileMenuIsOpen"
        x-transition:enter="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
        id="mobileMenu"
        role="menu"
    >
        @foreach ($navigations as $navigation)
            @php
                $hasChild = $navigation->children->count() > 0;
            @endphp
            <li @class([
                    'w-full flex flex-col items-center justify-between',
                ])
                @if ($hasChild)
                    x-data="{ isExpanded: {{ $navigation->has_active ? 'true' : 'false' }} }"
                    aria-controls="accordionItem{{$navigation->id}}"
                    :aria-expanded="isExpanded ? 'true' : 'false'"
                    aria-haspopup="true"
                @endif
                role="menuitem"
            >
                <a @class([
                        'flex w-full h-14 justify-between items-center px-2 font-bold text-white gap-2',
                        'bg-primary-600' => $navigation->has_active,
                    ])
                    @if ($hasChild)
                        @click="isExpanded = ! isExpanded"
                        href="javascript:;"
                    @else
                        {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                    @endif
                >
                    {{ $navigation->name_label }}
                    @if ($hasChild)
                        <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
                    @endif
                </a>

                @if ($hasChild) 
                    <ul class="w-full flex flex-col border-t border-primary-400 divide-y divide-primary-400"
                        id="accordionItem{{$navigation->id}}"
                        x-cloak x-show="isExpanded"
                        aria-labelledby="controlsAccordionItemOne{{$navigation->id}}"
                        x-collapse
                        role="menu"
                    >
                        @foreach ($navigation->children as $child)
                            @php
                                $hasGrandChild = $child->children->count() > 0;
                            @endphp
                            <li @class([
                                    'w-full flex flex-col items-center justify-between',
                                ])
                                @if ($hasGrandChild)
                                    x-data="{ isExpanded: {{ $child->has_active ? 'true' : 'false' }} }"
                                    aria-controls="accordionChildItem{{$child->id}}"
                                    :aria-expanded="isExpanded ? 'true' : 'false'"
                                    aria-haspopup="true"
                                @endif
                                role="menuitem"
                            >
                                <a @class([
                                        'flex w-full h-14 justify-between items-center pl-8 pr-2 font-bold text-white gap-2',
                                        'bg-primary-600' => $child->has_active,
                                    ])
                                    @if ($hasGrandChild)
                                        @click.stop="isExpanded = ! isExpanded"
                                        href="javascript:;"
                                    @else
                                        {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                    @endif
                                >
                                    {{ $child->name_label }}
                                    @if ($hasGrandChild)
                                        <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
                                    @endif
                                </a>

                                @if ($hasGrandChild) 
                                    <ul class="w-full flex flex-col border-t border-primary-400 divide-y divide-primary-400"
                                        id="accordionChildItem{{$child->id}}"
                                        x-cloak x-show="isExpanded"
                                        aria-labelledby="controlsAccordionItemTwo{{$child->id}}"
                                        x-collapse
                                        role="menu"
                                    >
                                        @foreach ($child->children as $grandChild)
                                            <li @class([
                                                    'w-full flex flex-col items-center justify-between',
                                                ])
                                                role="menuitem"
                                            >
                                                <a @class([
                                                        'flex w-full h-14 justify-between items-center pl-16 pr-2 font-bold text-white gap-2',
                                                        'bg-primary-600' => $grandChild->has_active,
                                                    ])
                                                    {{ \Filament\Support\generate_href_html($grandChild->url_info['url'], $grandChild->url_info['target'] ?? false) }}
                                                >
                                                    {{ $grandChild->name_label }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif 
                            </li>
                        @endforeach
                    </ul>
                @endif 
            </li>
        @endforeach
    </ul>
</nav>