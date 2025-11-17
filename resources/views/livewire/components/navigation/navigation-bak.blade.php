@php
    $navigations = $this->getNavigations();
@endphp

<nav class="w-full bg-primary-500" x-data="{ mobileMenuIsOpen: false }" @click.away="mobileMenuIsOpen = false">
    <div class="container mx-auto relative px-4 sm:px-0">
        <div class="flex justify-between h-16">
            <div class="flex gap-4">
                <ul class="hidden md:flex" role="menu">
                    @foreach ($navigations as $navigation)
                        @if ($navigation->children->count() > 0)
                            <li @class([
                                    'min-w-32 flex items-center relative w-fit group/child hover:bg-primary-600 transition-colors duration-300 ease-in-out',
                                    'bg-primary-600' => $navigation->has_active,
                                ])
                                x-data="{ isOpen: false, openedWithKeyboard: false, leaveTimeout: null }"
                                x-on:mouseleave.prevent="leaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                                x-on:mouseenter="leaveTimeout ? clearTimeout(leaveTimeout) : true"
                                x-on:keydown.esc.prevent="isOpen = false, openedWithKeyboard = false"
                                x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                                role="menuitem"
                            >
                                <a class="flex w-full h-full justify-center items-center relative px-2 font-bold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                                    href="javascript:;"
                                    x-on:mouseover="isOpen = true"
                                    x-on:keydown.space.prevent="openedWithKeyboard = true"
                                    x-on:keydown.enter.prevent="openedWithKeyboard = true"
                                    x-on:keydown.down.prevent="openedWithKeyboard = true"
                                    x-bind:aria-expanded="isOpen || openedWithKeyboard"
                                    aria-haspopup="true"
                                >
                                    {{ $navigation->name_label }}
                                    <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300 rotate-0 group-hover/child:rotate-180" aria-hidden="true" />
                                </a>

                                <ul class="w-full absolute top-16 flex flex-col bg-primary-500 z-10"
                                    x-cloak x-show="isOpen || openedWithKeyboard"
                                    x-transition
                                    x-trap="openedWithKeyboard"
                                    x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                                    x-on:keydown.down.prevent="$focus.wrap().next()"
                                    x-on:keydown.up.prevent="$focus.wrap().previous()"
                                    role="menu"
                                >
                                    @foreach ($navigation->children as $child)
                                        @if ($child->children->count() > 0)
                                            <li @class([
                                                    'w-full group/grandchild hover:bg-primary-600 focus-visible:bg-primary-600 focus-visible:outline-hidden transition-colors duration-300 ease-in-out',
                                                    'bg-primary-600' => $child->has_active,
                                                ])
                                                x-data="{ isChildOpen: false, openedChildWithKeyboard: false, childLeaveTimeout: null }"
                                                x-on:mouseleave.prevent="childLeaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                                                x-on:mouseenter="childLeaveTimeout ? clearTimeout(childLeaveTimeout) : true"
                                                x-on:keydown.esc.prevent="isChildOpen = false, openedChildWithKeyboard = false"
                                                x-on:click.outside="isChildOpen = false, openedChildWithKeyboard = false"
                                                role="menuitem"
                                            >
                                                <a @class([
                                                        'h-14 flex items-center justify-center font-bold text-white',
                                                    ])
                                                    href="javascript:;"
                                                    x-on:mouseover="isChildOpen = true"
                                                    x-on:keydown.space.prevent="openedChildWithKeyboard = true"
                                                    x-on:keydown.enter.prevent="openedChildWithKeyboard = true"
                                                    x-on:keydown.down.prevent="openedChildWithKeyboard = true"
                                                    x-bind:aria-expanded="isChildOpen || openedChildWithKeyboard"
                                                    aria-haspopup="true"
                                                >
                                                    {{ $child->name_label }}
                                                    <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300 rotate-0 group-hover/grandchild:-rotate-90" aria-hidden="true" />
                                                </a>

                                                <ul class="w-full absolute top-0 left-full flex flex-col overflow-hidden bg-primary-500 z-11"
                                                    x-cloak x-show="isChildOpen || openedChildWithKeyboard"
                                                    x-transition
                                                    x-trap="openedChildWithKeyboard"
                                                    x-on:click.outside="isChildOpen = false, openedChildWithKeyboard = false"
                                                    x-on:keydown.down.prevent="$focus.wrap().next()"
                                                    x-on:keydown.up.prevent="$focus.wrap().previous()"
                                                    role="menu"
                                                >
                                                    @foreach ($child->children as $grandChild)
                                                        <li @class([
                                                                'w-full hover:bg-primary-600 focus-visible:bg-primary-600 focus-visible:outline-hidden transition-colors duration-300 ease-in-out',
                                                                'bg-primary-600' => $grandChild->has_active,
                                                            ])
                                                            role="menuitem"
                                                        >
                                                            <a @class([
                                                                    'w-full h-14 flex items-center justify-center font-bold text-white',
                                                                ])
                                                                {{ \Filament\Support\generate_href_html($grandChild->url_info['url'], $grandChild->url_info['target'] ?? false) }}
                                                            >
                                                                {{ $grandChild->name_label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li @class([
                                                    'w-full hover:bg-primary-600 focus-visible:bg-primary-600 focus-visible:outline-hidden transition-colors duration-300 ease-in-out',
                                                    'bg-primary-600' => $child->has_active,
                                                ])
                                                role="menuitem"
                                            >
                                                <a @class([
                                                    'w-full h-14 flex items-center justify-center font-bold text-white '
                                                ])
                                                    {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                                >
                                                    {{ $child->name_label }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li @class([
                                    'min-w-32 flex items-center hover:bg-primary-600 transition-colors duration-300 ease-in-out',
                                    'bg-primary-600' => $navigation->has_active,
                                ])
                                role="menuitem"
                            >
                                <a class="flex w-full h-full justify-center items-center font-bold text-white underline-offset-2 focus:outline-hidden focus:underline"
                                    {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                                >
                                    {{ $navigation->name_label }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Settings Dropdown -->
            {{-- <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-filament::dropdown>
                    <x-slot name="trigger">
                        <x-base.operator class="flex items-center justify-center px-1">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <x-heroicon-m-chevron-down class="w-5 h-5 font-bold" />
                            </div>
                        </x-base.operator>
                    </x-slot>

                    <x-filament::dropdown.list>
                        <x-filament::dropdown.list.item :href="route('sn-shop.index')" tag="a">
                            {{ __('Profile') }}
                        </x-filament::dropdown.list.item>

                        <x-filament::dropdown.list.item wire:click="logout">
                            {{ __('Log Out') }}
                        </x-filament::dropdown.list.item>
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            </div> --}}
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
        <ul class="fixed max-h-svh overflow-y-auto divide-y divide-primary-400 inset-x-0 top-0 z-10 flex flex-col rounded-b-md bg-primary-500 pb-6 pt-20 md:hidden"
            x-cloak x-show="mobileMenuIsOpen"
            x-transition:enter="transition motion-reduce:transition-none ease-out duration-300"
            x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition motion-reduce:transition-none ease-out duration-300"
            x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
            id="mobileMenu"
        >
            @foreach ($navigations as $navigation)
                @if ($navigation->children->count() > 0)
                    <li>
                        <div x-data="{ isExpanded: {{ $navigation->has_active ? 'true' : 'false' }} }">
                            <button id="controlsAccordionItem{{$navigation->id}}" type="button"
                                @class([
                                    'flex w-full items-center justify-between gap-4 p-4 font-bold text-white underline-offset-2 focus-visible:underline focus-visible:outline-none',
                                    'bg-primary-600' => $navigation->has_active,
                                ])
                                aria-controls="accordionItem{{$navigation->id}}"
                                @click="isExpanded = ! isExpanded"
                                :aria-expanded="isExpanded ? 'true' : 'false'">
                                {{ $navigation->name_label }}
                                <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 transform transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
                            </button>
                            <div class="flex flex-col border-t border-primary-400 divide-y divide-primary-400"
                                id="accordionItem{{$navigation->id}}"
                                x-cloak x-show="isExpanded"
                                x-collapse
                                role="menu"
                                aria-labelledby="controlsAccordionItemOne{{$navigation->id}}"
                            >
                                @foreach ($navigation->children as $child)
                                    <a @class([
                                            'flex w-full h-full p-4 font-bold text-white',
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
                    <li class="flex">
                        <a @class([
                                'flex grow p-4 font-bold text-white focus:underline',
                                'bg-primary-600' => $navigation->has_active,
                            ])
                            {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                            role="menuitem"
                            aria-current="page"
                        >
                            {{ $navigation->name_label }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>