<x-dynamic-component :component="$wrapperView" class="w-full flex flex-col md:flex-row gap-4" x-data="indexPosts({})">
    <div class="h-96 relative overflow-hidden flex-1 gap-4">
        <div class="swiper news-swiper">
            <div class="swiper-wrapper">
                @foreach($posts as $post)
                    @if ($loop->index < ($limit / 2))
                        <div class="swiper-slide relative">
                            <img src="{{ $post->getFirstMediaUrl('main', 'thumb') }}" class="w-full h-full object-cover" @click="toJump('{{ sn_route('posts.show', $post->id) }}')" />
                            <div class="absolute right-0 bottom-0 left-0 z-10 leading-[62px] text-[18px] text-[#FFFFFF] pl-[24px] line-clamp-1 pr-[90px] text-left" style="background: rgba(0,0,0,0.5)">{{ $post->title }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="swiper-pagination" style="bottom: 0 !important; right: 0 !important; text-align: right; height: 62px; padding-right: 18px;"></div>
        </div>
    </div>

    @if ($posts->count() > ($limit / 2))
        <div class="h-96 flex flex-col flex-1 gap-4">
            @foreach($posts as $post)
                @if ($loop->index >= ($limit / 2))
                    <x-dynamic-component :component="$itemWrapperView" tag="a" href="{{ sn_route('posts.show', $post->id) }}" class="flex items-center">
                        <div class="w-[176px] h-[100px]">
                            <img src="{{ $post->getFirstMediaUrl('main', 'thumb') }}" class="w-full h-full object-cover" />
                        </div>
                        <div class="h-[100px] bg-[#F3E8DB] box-border pl-[16px] pr-[20px] pt-[10px]">
                            <div class="text-[#3C2F21] text-[18px] leading-[18px] line-clamp-1 text-left">
                                {{ $post->title }}
                            </div>
                            <div class="mt-[14px] text-[#666666] text-[16px] leading-[16px] line-clamp-1 text-left">
                                {{ $post->description }}
                            </div>
                            <div class="mt-[14px] text-[#999999] text-[14px] leading-[14px] text-left">
                                {{ optional($post->updated_at)->format('Y-m-d') }}
                            </div>
                        </div>
                    </x-dynamic-component>
                @endif
            @endforeach
        </div>
    @endif
</x-dynamic-component>

@assets

<script>
    function indexPosts({}) {
        return {
            newsSwiper: null,
            init() {
                // 初始化新闻轮播
                this.newsSwiper = new Swiper('.news-swiper', {
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    // effect: 'fade',
                    // fadeEffect: {
                    //     crossFade: true
                    // },
                    speed: 800,
                    mousewheel: {
                        enabled: false
                    }
                });
            },
            toJump(route) {
                Livewire.navigate(route)
            },
        }
    }
</script>
@endassets