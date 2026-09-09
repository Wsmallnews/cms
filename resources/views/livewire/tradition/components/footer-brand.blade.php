{{--
    footer 品牌区（与 footer.blade.php 同目录，经 @include 引入，随主题目录整体切换）：
    品牌（logo + 站名，与页头统一逻辑）+ 口号 + 联系方式（单列）+ 二维码（贴底）
    数据：$general（GeneralSettings）
--}}
@php
    $siteName = filled($general->site_name) ? $general->site_name : config('app.name');

    // logo 默认为空字符串，files_url('') 会得到站点根地址，需 filled 守卫
    $logoUrl = filled($general->logo) ? files_url($general->logo) : null;
@endphp

<div class="flex flex-col flex-1 gap-3">
    <x-sn-cms::brand
        :logo-url="$logoUrl"
        :site-name="$siteName"
        :with-name="$general->logo_with_site_name"
        size="footer"
    />

    {{-- 口号单独一行，只要填写就显示 --}}
    @if (filled($general->site_slogan))
        <p class="sn-descript-text">{{ $general->site_slogan }}</p>
    @endif

    <address class="not-italic flex flex-col gap-1.5">
        @if ($general->phone)
            <div class="flex items-baseline gap-2">
                <span class="sn-tip-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.contact_phone') }}</span>
                <a href="tel:{{ $general->phone }}" class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm transition-colors">{{ $general->phone }}</a>
            </div>
        @endif
        @if ($general->email)
            <div class="flex items-baseline gap-2">
                <span class="sn-tip-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.contact_email') }}</span>
                <a href="mailto:{{ $general->email }}" class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm break-all transition-colors">{{ $general->email }}</a>
            </div>
        @endif
        @if ($general->address)
            <div class="flex items-baseline gap-2">
                <span class="sn-tip-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.contact_address') }}</span>
                <span class="sn-content-text">{{ $general->address }}</span>
            </div>
        @endif
        @if ($general->work_time)
            <div class="flex items-baseline gap-2">
                <span class="sn-tip-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.work_time') }}</span>
                <span class="sn-content-text">{{ $general->work_time }}</span>
            </div>
        @endif
    </address>

    @if ($general->wechat_qrcode || $general->wechat_official_qrcode)
        <div class="mt-auto flex gap-4">
            @if ($general->wechat_official_qrcode)
                <figure class="flex flex-col items-center gap-1 group">
                    <img class="w-[72px] h-[72px] rounded-lg sn-ring-card bg-gray-50 dark:bg-gray-800 p-1.5 transition-transform duration-300 group-hover:scale-105" src="{{ files_url($general->wechat_official_qrcode) }}" alt="{{ __('sn-cms::cms.frontend.official_qrcode') }}" loading="lazy" />
                    <figcaption class="sn-tip-text">{{ __('sn-cms::cms.frontend.official_account') }}</figcaption>
                </figure>
            @endif
            @if ($general->wechat_qrcode)
                <figure class="flex flex-col items-center gap-1 group">
                    <img class="w-[72px] h-[72px] rounded-lg sn-ring-card bg-gray-50 dark:bg-gray-800 p-1.5 transition-transform duration-300 group-hover:scale-105" src="{{ files_url($general->wechat_qrcode) }}" alt="{{ __('sn-cms::cms.frontend.wechat_qrcode') }}" loading="lazy" />
                    <figcaption class="sn-tip-text">{{ __('sn-cms::cms.frontend.personal_wechat') }}</figcaption>
                </figure>
            @endif
        </div>
    @endif
</div>
