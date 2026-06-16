@php
    use Filament\Support\Icons\Heroicon;
    use Wsmallnews\Cms\Facades\FlagRegistry;
    $flags = FlagRegistry::getTypes($scopeType);

    $view = $this->getThemeView('components.category.categories');
    $recordView = $this->getBladeThemeView('components.category.category-record');
@endphp

<div class="w-full flex flex-col lg:flex-row gap-4 relative">
    <x-sn-support::loading.overlay />

    @if ($categoryStyle == 'tree')
        <div class="w-full lg:w-72">
            <livewire:sn-category-components-categories
                :scope-type="$scopeType"
                :use-url="false"
                :active-category-id="$categoryId"
                :view="$view"
                :record-view="$recordView"
                :key="'sn-category-components-categories-' . $this->getFingerprint()" />
        </div>
    @endif

    <div class="w-full flex flex-col gap-4 grow">
        @if ($categoryStyle == 'select' && $categories->isNotEmpty())
            <div class="flex flex-wrap gap-4">
                @foreach ($categories as $category)
                    <x-filament::badge class="sn-primary-bg text-sm text-white">{{ $category->name_label }}</x-filament::badge>
                @endforeach
            </div>
        @endif

        <x-filament::tabs label="flags">
            <x-filament::tabs.item
                wire:click="$set('flag', '')"
                :active="blank($flag)"
            >
                {{ __('sn-cms::cms.frontend.all') }}
            </x-filament::tabs.item>
            @foreach ($flags as $flagItem)
                <x-filament::tabs.item
                    wire:click="$set('flag', '{{ $flagItem['type'] }}')"
                    :active="$flagItem['type'] == $flag"
                    :icon="$flagItem['icon']"
                >
                    {{ $flagItem['label'] }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        <div class="w-full flex flex-row-reverse gap-4">
            <x-filament::input.wrapper
                class="w-full md:w-80"
                inline-prefix
                :prefix-icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass"
            >
                <label for="post-search" class="sr-only">{{ __('sn-cms::cms.frontend.search') }}</label>
                <x-filament::input
                    id="post-search"
                    type="search"
                    placeholder="{{ __('sn-cms::cms.frontend.search_placeholder') }}"
                    wire:model.live.debounce.250ms="search"
                />
            </x-filament::input.wrapper>
        </div>

        <x-sn-support::paginators.container :page-type="$pageType" :page-info="$pageInfo" :paginator-link="$paginatorLink" :page-name="$pageName">
            <div class="w-full flex flex-col gap-4">
                @foreach ($posts as $post)
                    <x-sn-cms::container.block-link
                        @class([
                            'sn-container sn-hover sn-link p-4' => $contained,
                            'flex flex-row gap-4 overflow-hidden group'
                        ])
                        href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post) }}"
                    >
                        <div class="w-32 h-32 sm:w-44 sm:h-44 shrink-0 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-800">
                            @if ($post->getFirstMediaUrl('post_image'))
                                <img class="sn-motion-scale w-full h-full object-cover" src="{{ $post->getFirstMediaUrl('post_image') }}" alt="{{ $post->title }}" loading="lazy" />
                            @else
                                <div class="sn-image-placeholder sn-motion-scale">
                                    <x-filament::icon :icon="Heroicon::OutlinedPhoto" class="w-10 h-10" aria-hidden="true" />
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col grow py-4 pr-4 gap-4">
                            <div class="sn-h3-text sn-hover line-clamp-1 transition duration-300">
                                {{ $post->title }}
                            </div>

                            <div class="sn-descript-text grow max-h-14 line-clamp-2">
                                {{ $post->description }}
                            </div>
                            <div class="sn-tip-text">
                                {{ $post->updated_at->format('Y-m-d') }}
                            </div>
                        </div>
                    </x-sn-cms::container.block-link>
                @endforeach
            </div>
        </x-sn-support::paginators.container>
    </div>
</div>