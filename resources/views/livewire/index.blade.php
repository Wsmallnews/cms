@php
    // 这里可以添加 PHP 代码
@endphp

{{-- @push('seo')
    {!! seo() !!}
@endpush --}}

<div class="w-full flex flex-col grow gap-4" x-data="index({})">
    <livewire:sn-components-navigation />

    <div class="container mx-auto flex flex-col grow gap-4">
        {{-- <livewire:sn-components-index-posts :limit="6" /> --}}
    </div>

    <livewire:sn-components-footer />
</div>

@assets
    <script>
        function index({

        }) {
            return {
                init() {
                    // this.pageInit()
                },
                // toJump(route) {
                //     Livewire.navigate(route)
                // },
                // pageInit() {
                //     // 初始化首页轮播
                //     const swiper = new Swiper('.first-screen-swiper', {
                //         loop: true,
                //         slidesPerView: 1,
                //         spaceBetween: 0,
                //         pagination: {
                //             el: '.swiper-pagination',
                //             clickable: true,
                //         },
                //         navigation: {
                //             nextEl: '.swiper-button-next',
                //             prevEl: '.swiper-button-prev',
                //         },
                //         effect: 'slide',
                //         speed: 800,
                //         grabCursor: true,
                //         keyboard: {
                //             enabled: true,
                //         },
                //         mousewheel: {
                //             enabled: false,
                //         },
                //         allowTouchMove: true,
                //         touchRatio: 1,
                //         touchAngle: 45,
                //         resistance: true,
                //         resistanceRatio: 0.85,
                //         observer: true,
                //         observeParents: true,
                //         autoplay: {
                //             delay: 3000, // 设置自动切换的时间间隔，单位为毫秒
                //             stopOnLastSlide: false, // 设置为true时，在最后一个Slide处停止自动轮播
                //         },
                //     });
                //     // 监听窗口大小变化
                //     window.addEventListener('resize', function() {
                //         swiper.update();
                //     });
                // }
            }
        }
    </script>

@endassets
