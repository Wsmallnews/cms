<div class="w-full">
    <div class="container mx-auto flex flex-col gap-4">
        <div class="w-full flex gap-16">
            <div class="hidden md:flex md:w-2/3 gap-4 justify-between">
                @foreach ($navigations as $navigation)
                    <div class="flex flex-col justify-start gap-4">
                        <a class="inline-block text-gray-800 font-bold text-xl leading-6 hover:text-primary-600"
                            @if ($navigation->children->count() > 0)
                                href="javascript:;"
                            @else
                                {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                            @endif
                        >
                            {{ $navigation->name }}
                        </a>
                        <div class="bg-primary-600 w-9 h-0.75"></div>
                        @if ($navigation->children->count() > 0)
                            <div class="flex flex-col justify-start gap-4">
                                @foreach ($navigation->children as $child)
                                    <a class="text-gray-600 text-base leading-5 items-center hover:text-primary-600"
                                        {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}>
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="w-full md:w-1/3 flex flex-col px-4 gap-4">
                <div class="text-gray-800 font-bold text-xl text-center leading-6">关注或联系我们</div>
                <div class="text-gray-600 text-base text-center leading-5">添加微信或关注官方微信</div>
                {{-- <div class="flex justify-center">
                    @if ($general->wechat_qrcode)
                        <img class="w-[100px] h-[100px] mr-[12px]" src="{{ files_url($general->wechat_qrcode) }}" />
                    @endif
                    @if ($general->wechat_official_qrcode)
                        <img class="w-[100px] h-[100px]" src="{{ files_url($general->wechat_official_qrcode) }}" />
                    @endif
                </div>
                @if ($general->phone)
                    <div class="flex items-center justify-start">
                        <div class="min-w-20 text-gray-600 text-base leading-5">联系电话：</div>
                        <div class="text-gray-600 text-lg leading-6">{{ $general->phone }}</div>
                    </div>
                @endif
                @if ($general->email)
                    <div class="flex items-center justify-start">
                        <div class="min-w-20 text-gray-600 text-base leading-5">联系邮箱：</div>
                        <div class="text-gray-600 text-lg leading-6">{{ $general->email }}</div>
                    </div>
                @endif
                @if ($general->address)
                    <div class="flex items-center justify-start">
                        <div class="min-w-20 text-gray-600 text-base leading-5">联系地址：</div>
                        <div class="text-gray-600 text-lg leading-6">{{ $general->address }}</div>
                    </div>
                @endif --}}
            </div>
        </div>

        <div class="w-full h-12 border-t border-gray-300 flex items-center justify-center text-base text-gray-500">
            {{-- Copyright © {{ $general->copytime }} {{ $general->copyright }} 版权所有 &nbsp;
            <a href="{{ $general->beian_url }}" target="_blank">{{ $general->beian_no }}</a> --}}
        </div>
    </div>
</div>
