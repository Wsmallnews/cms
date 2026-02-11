<div class="w-full flex flex-col gap-4">
    @if ($categories->isNotEmpty())
        <div class="flex flex-wrap gap-4">
            @foreach ($categories as $category)
                <x-filament::badge class="text-sm bg-primary-500 text-white">{{ $category->name_label }}</x-filament::badge>
            @endforeach
        </div>
    @endif

    <x-sn-support::paginators.container :page-type="$pageType" :page-info="$pageInfo" :paginator-link="$paginatorLink" :page-name="$pageName">
        <div class="w-full flex flex-col gap-4">
            @foreach ($posts as $post)
                <x-sn-cms::container.block-link 
                    @class([
                        'sn-container sn-hover sn-link p-4' => $contained,
                        'flex flex-row gap-4 overflow-hidden group'
                    ])
                    href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post->id) }}"
                >
                    @if ($post->getFirstMediaUrl('post_image'))
                        <div class="w-44 h-44 shrink-0 rounded-md overflow-hidden">
                            <img class="w-full h-full object-cover transition duration-300 group-hover:scale-110" src="{{ $post->getFirstMediaUrl('post_image') }}" />
                        </div>
                    @endif

                    <div class="flex flex-col grow py-4 pr-4 gap-4">
                        <div class="text-xl font-bold line-clamp-1 transition duration-300 group-hover:text-primary-500">
                            {{ $post->title }}
                        </div>

                        <div class="grow max-h-14 text-gray-500 leading-7 line-clamp-2">
                            {{ $post->description }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $post->updated_at->format('Y-m-d') }}
                        </div>
                    </div>
                </x-sn-cms::container.block-link>
            @endforeach
        </div>
    </x-sn-support::paginators.container>
</div>