@php
    $navigations = $this->getNavigations();
@endphp

<nav class="w-full bg-primary-500" x-data="{ mobileMenuIsOpen: false }" @click.away="mobileMenuIsOpen = false">
    <div class="container mx-auto relative px-4 sm:px-0">
        <div class="flex justify-between h-16">
            <div class="flex gap-4">
                {{-- <div class="shrink-0 flex items-center">
                    <a href="{{ sn_route('index') }}" wire:navigate>
                        <x-filament-panels::logo class="" />
                    </a>
                </div> --}}
                <ul class="hidden md:-my-px md:flex">
                    @foreach ($navigations as $navigation)
                        @if ($navigation->children->count() > 0)
                            <li class="min-w-32 flex items-center relative w-fit hover:bg-primary-600"
                                x-data="{ isOpen: false, openedWithKeyboard: false, leaveTimeout: null }"
                                x-on:mouseleave.prevent="leaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                                x-on:mouseenter="leaveTimeout ? clearTimeout(leaveTimeout) : true"
                                x-on:keydown.esc.prevent="isOpen = false, openedWithKeyboard = false"
                                x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                            >
                                <a class="flex w-full h-full justify-center items-center relative font-bold text-white underline-offset-2 focus:outline-hidden focus:underline group"
                                    href="javascript:;"
                                    x-on:mouseover="isOpen = true"
                                    x-on:keydown.space.prevent="openedWithKeyboard = true"
                                    x-on:keydown.enter.prevent="openedWithKeyboard = true"
                                    x-on:keydown.down.prevent="openedWithKeyboard = true"
                                    x-bind:aria-expanded="isOpen || openedWithKeyboard"
                                    aria-haspopup="true"
                                >
                                    {{ $navigation->name }}
                                    <svg class="absolute right-2 size-4 transform transition-transform duration-300 rotate-0 group-hover:rotate-180" aria-hidden="true" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </a>

                                <div class="w-full absolute top-16 flex flex-col overflow-hidden bg-primary-500 z-10"
                                    x-cloak x-show="isOpen || openedWithKeyboard"
                                    x-transition
                                    x-trap="openedWithKeyboard"
                                    x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                                    x-on:keydown.down.prevent="$focus.wrap().next()"
                                    x-on:keydown.up.prevent="$focus.wrap().previous()"
                                    role="menu"
                                >
                                    @foreach ($navigation->children as $child)
                                        <a class="h-12 flex items-center justify-center font-bold text-white hover:bg-primary-600 focus-visible:bg-primary-600 focus-visible:outline-hidden"
                                            {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                            role="menuitem"
                                        >
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @else
                            <li class="min-w-32 flex items-center hover:bg-primary-600">
                                <a class="flex w-full h-full justify-center items-center font-bold text-white underline-offset-2 focus:outline-hidden focus:underline"
                                    {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                                >
                                    {{ $navigation->name }}
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
            <svg x-cloak x-show="!mobileMenuIsOpen" xmlns="http://www.w3.org/2000/svg" fill="none" aria-hidden="true" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <svg x-cloak x-show="mobileMenuIsOpen" xmlns="http://www.w3.org/2000/svg" fill="none" aria-hidden="true" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
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
                        <div x-data="{ isExpanded: false }">
                            <button id="controlsAccordionItem{{$navigation->id}}" type="button"
                                class="flex w-full items-center justify-between gap-4 p-4 font-bold text-white underline-offset-2 focus-visible:underline focus-visible:outline-none"
                                aria-controls="accordionItem{{$navigation->id}}"
                                @click="isExpanded = ! isExpanded"
                                :aria-expanded="isExpanded ? 'true' : 'false'">
                                {{ $navigation->name }}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                                    stroke="currentColor" class="size-5 shrink-0 transition" aria-hidden="true"
                                    :class="isExpanded  ?  'rotate-180'  :  ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div class="flex flex-col px-2 border-t border-primary-400 divide-y divide-primary-400"
                                id="accordionItem{{$navigation->id}}"
                                x-cloak x-show="isExpanded"
                                x-collapse
                                role="menu"
                                aria-labelledby="controlsAccordionItemOne{{$navigation->id}}"
                            >
                                @foreach ($navigation->children as $child)
                                    <a class="flex w-full h-full px-4 py-4 font-bold text-white"
                                        {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                        role="menuitem"
                                    >
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                @else
                    <li class="flex">
                        <a class="flex grow px-4 py-4 font-bold text-white focus:underline"
                            {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                            aria-current="page"
                        >
                            {{ $navigation->name }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>