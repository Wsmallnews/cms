@php
    use Wsmallnews\Cms\Facades\FlagRegistry;
    $flags = FlagRegistry::getTypes($scopeType);

    $currentUrl = request()->fullUrlWithoutQuery('flag');
    $flagUrl = $currentUrl . (Str::contains($currentUrl, '?') ? '&' : '?') . 'flag=';

    $view = $this->getThemeView('components.category.categories');
    $recordView = $this->getBladeThemeView('components.category.category-record');
@endphp

<div class="w-full flex flex-col lg:flex-row gap-4">
    @if ($categoryStyle == 'tree')
        <div class="w-full lg:w-72">
            <livewire:sn-category-components-categories :scope-type="$scopeType" :use-url="true" :view="$view" :record-view="$recordView" />
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
                tag="a"
                :href="$currentUrl"
                :active="blank($flag)"
            >
                全部
            </x-filament::tabs.item>
            @foreach ($flags as $flagItem)
                <x-filament::tabs.item
                    tag="a"
                    :href="$flagUrl . $flagItem['type']"
                    :active="$flagItem['type'] == $flag"
                    :icon="$flagItem['icon']"
                >
                    {{ $flagItem['label'] }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

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
                        @if ($post->getFirstMediaUrl('post_image'))
                            <div class="w-44 h-44 shrink-0 rounded-md overflow-hidden">
                                <img class="w-full h-full object-cover transition duration-300 group-hover:scale-110" src="{{ $post->getFirstMediaUrl('post_image') }}" />
                            </div>
                        @endif

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