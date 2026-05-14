<footer class="sn-bg sn-contour-only border-t-2 w-full pt-10 mt-12">
    <div class="container mx-auto flex flex-col gap-6 px-4">
        <div class="w-full flex flex-col md:flex-row gap-10 md:gap-16">
            <nav class="hidden md:flex md:w-2/3 gap-8 justify-between" aria-label="{{ __('sn-cms::cms.frontend.footer_nav') }}">
                @foreach ($navigations as $navigation)
                    <div class="flex flex-col justify-start gap-3">
                        <a class="sn-h3-text sn-hover inline-block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm"
                            @if ($navigation->children->count() > 0)
                                href="javascript:;"
                                role="presentation"
                            @else
                                {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                            @endif
                        >
                            {{ $navigation->name_label }}
                        </a>
                        <span class="sn-primary-bg w-9 h-1 rounded-full" aria-hidden="true"></span>
                        @if ($navigation->children->count() > 0)
                            <ul class="flex flex-col justify-start gap-2.5" role="list">
                                @foreach ($navigation->children as $child)
                                    <li>
                                        <a class="sn-content-text sn-hover items-center focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm"
                                            {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}>
                                            {{ $child->name_label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </nav>

            <div class="w-full md:w-1/3 flex flex-col gap-4">
                <h2 class="sn-h3-text text-center">{{ __('sn-cms::cms.frontend.follow_us') }}</h2>
                <p class="sn-content-text text-center">{{ __('sn-cms::cms.frontend.follow_us_desc') }}</p>
                <div class="flex justify-center gap-3">
                    @if ($general->wechat_qrcode)
                        <figure class="flex flex-col items-center gap-1">
                            <img class="w-[100px] h-[100px] rounded-md ring-1 ring-gray-200 dark:ring-gray-700" src="{{ files_url($general->wechat_qrcode) }}" alt="{{ __('sn-cms::cms.frontend.wechat_qrcode') }}" loading="lazy" />
                            <figcaption class="sn-tip-text">{{ __('sn-cms::cms.frontend.personal_wechat') }}</figcaption>
                        </figure>
                    @endif
                    @if ($general->wechat_official_qrcode)
                        <figure class="flex flex-col items-center gap-1">
                            <img class="w-[100px] h-[100px] rounded-md ring-1 ring-gray-200 dark:ring-gray-700" src="{{ files_url($general->wechat_official_qrcode) }}" alt="{{ __('sn-cms::cms.frontend.official_qrcode') }}" loading="lazy" />
                            <figcaption class="sn-tip-text">{{ __('sn-cms::cms.frontend.official_account') }}</figcaption>
                        </figure>
                    @endif
                </div>
                <address class="not-italic">
                    <dl class="flex flex-col gap-2">
                        @if ($general->phone)
                            <div class="flex items-center justify-start">
                                <dt class="sn-content-text min-w-20">{{ __('sn-cms::cms.frontend.contact_phone') }}：</dt>
                                <dd class="sn-content-text">
                                    <a href="tel:{{ $general->phone }}" class="hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">{{ $general->phone }}</a>
                                </dd>
                            </div>
                        @endif
                        @if ($general->email)
                            <div class="flex items-center justify-start">
                                <dt class="sn-content-text min-w-20">{{ __('sn-cms::cms.frontend.contact_email') }}：</dt>
                                <dd class="sn-content-text">
                                    <a href="mailto:{{ $general->email }}" class="hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">{{ $general->email }}</a>
                                </dd>
                            </div>
                        @endif
                        @if ($general->address)
                            <div class="flex items-start justify-start">
                                <dt class="sn-content-text min-w-20 shrink-0">{{ __('sn-cms::cms.frontend.contact_address') }}：</dt>
                                <dd class="sn-content-text">{{ $general->address }}</dd>
                            </div>
                        @endif
                    </dl>
                </address>
            </div>
        </div>

        <div class="w-full py-4 mt-2 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-center gap-1 sn-tip-text text-center">
            <span>{{ __('sn-cms::cms.frontend.copyright', ['copytime' => $general->copytime, 'copyright' => $general->copyright]) }}</span>
            @if ($general->beian_url && $general->beian_no)
                <a href="{{ $general->beian_url }}" target="_blank" rel="noopener" class="hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">
                    {{ $general->beian_no }}
                </a>
            @endif
        </div>
    </div>
</footer>
