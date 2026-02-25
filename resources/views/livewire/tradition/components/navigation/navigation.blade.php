@php
    $nestedset = $this->getNestedset();
@endphp

<nav class="sn-primary-bg w-full" x-data="{ mobileMenuIsOpen: false }" @click.away="mobileMenuIsOpen = false">
    <div class="container hidden md:flex h-16 mx-auto px-4 sm:px-0">
        <ul class="flex h-full" role="menu">
            @foreach ($nestedset as $navigation)
                @php
                    $hasChild = $navigation->children->count() > 0;
                @endphp
                <li @class([
                        'sn-primary-bg sn-hover min-w-32 flex items-center relative group/child',
                        'sn-active' => $navigation->has_active,
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
                    <a class="flex w-full h-full justify-center items-center px-4 font-semibold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
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
                            <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-semibold transform transition-transform duration-300 rotate-0 group-hover/child:rotate-180" aria-hidden="true" />
                        @endif
                    </a>

                    @if ($hasChild) 
                        <div class="sn-primary-bg w-full absolute top-full left-0 z-10"
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
                                            'sn-primary-bg sn-hover w-full h-14 flex items-center relative group/grandchild',
                                            'sn-active' => $child->has_active,
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
                                        <a class="flex w-full h-full justify-between items-center px-4 font-semibold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
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
                                                <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-semibold transform transition-transform duration-300 rotate-0 group-hover/child:-rotate-90" aria-hidden="true" />
                                            @endif
                                        </a>

                                        @if ($hasGrandChild) 
                                            <div class="sn-primary-bg w-full absolute top-0 left-full"
                                                x-cloak x-show="isOpen || openedWithKeyboard"
                                                x-transition
                                                x-trap="openedWithKeyboard"
                                            >
                                                <ul class="flex flex-col" role="menu">
                                                    @foreach ($child->children as $grandChild)
                                                        <li @class([
                                                                'sn-primary-bg sn-hover w-full h-12 flex items-center',
                                                                'sn-active' => $grandChild->has_active,
                                                            ])
                                                            role="menuitem"
                                                        >
                                                            <a class="flex w-full h-full justify-between items-center px-4 font-semibold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                                                                
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
    <ul
        @class([
            'sn-primary-bg w-full flex flex-col fixed max-h-svh overflow-y-auto inset-x-0 top-0 z-10 rounded-b-md pb-6 pt-20 divide-y divide-primary-400 md:hidden',
        ])
        x-cloak x-show="mobileMenuIsOpen"
        x-transition:enter="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
        id="mobileMenu"
        role="menu"
    >
        @forelse($nestedset as $treeKey => $record)
            <x-dynamic-component 
                @class([
                    'w-full',
                ]) 
                :component="$this->getRecordView()" 
                key="categories-component-{{ $record->getKey() }}" 
                :record="$record" 
                :first="$loop->first" 
                :last="$loop->last" 
                :current-level="1" 
            />
        @empty
            <li class="w-full px-3 py-2 text-center">
                {{ $this->getEmptyLabel() ?: __('sn-filament-nestedset::nestedset.tree.empty_label')}}
            </li>
        @endforelse
    </ul>
</nav>