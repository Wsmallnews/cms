{{--
    footer 品牌区：品牌（logo + 站名，与页头统一逻辑）+ 口号（单独一行）+ 联系方式 + 二维码
    数据：GeneralSettings（site_name/logo/logo_with_site_name/site_slogan/联系方式/二维码）
--}}
@props([
    'general',
])

@php
    $siteName = filled($general->site_name) ? $general->site_name : config('app.name');

    // logo 默认为空字符串，files_url('') 会得到站点根地址，需 filled 守卫
    $logoUrl = filled($general->logo) ? files_url($general->logo) : null;
@endphp

<div class="flex flex-col gap-4">
    <x-sn-cms::brand
        :logo-url="$logoUrl"
        :site-name="$siteName"
        :with-name="$general->logo_with_site_name"
        size="footer"
    />

    {{-- 口号单独一行，只要填写就显示 --}}
    @if (filled($general->site_slogan))
        <p class="sn-descript-text -mt-2">{{ $general->site_slogan }}</p>
    @endif

    <address class="not-italic flex flex-col gap-1.5">
        @if ($general->phone)
            <div class="flex items-baseline gap-2">
                <span class="sn-descript-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.contact_phone') }}</span>
                <a href="tel:{{ $general->phone }}" class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">{{ $general->phone }}</a>
            </div>
        @endif
        @if ($general->email)
            <div class="flex items-baseline gap-2">
                <span class="sn-descript-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.contact_email') }}</span>
                <a href="mailto:{{ $general->email }}" class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">{{ $general->email }}</a>
            </div>
        @endif
        @if ($general->address)
            <div class="flex items-baseline gap-2">
                <span class="sn-descript-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.contact_address') }}</span>
                <span class="sn-content-text">{{ $general->address }}</span>
            </div>
        @endif
        @if ($general->work_time)
            <div class="flex items-baseline gap-2">
                <span class="sn-descript-text w-16 shrink-0">{{ __('sn-cms::cms.frontend.work_time') }}</span>
                <span class="sn-content-text">{{ $general->work_time }}</span>
            </div>
        @endif
    </address>

    @if ($general->wechat_qrcode || $general->wechat_official_qrcode)
        <div class="flex gap-4 pt-1">
            @if ($general->wechat_official_qrcode)
                <figure class="flex flex-col items-center gap-1.5">
                    <img class="w-[84px] h-[84px] rounded-lg sn-ring-card p-1.5" src="{{ files_url($general->wechat_official_qrcode) }}" alt="{{ __('sn-cms::cms.frontend.official_qrcode') }}" loading="lazy" />
                    <figcaption class="sn-tip-text">{{ __('sn-cms::cms.frontend.official_account') }}</figcaption>
                </figure>
            @endif
            @if ($general->wechat_qrcode)
                <figure class="flex flex-col items-center gap-1.5">
                    <img class="w-[84px] h-[84px] rounded-lg sn-ring-card p-1.5" src="{{ files_url($general->wechat_qrcode) }}" alt="{{ __('sn-cms::cms.frontend.wechat_qrcode') }}" loading="lazy" />
                    <figcaption class="sn-tip-text">{{ __('sn-cms::cms.frontend.personal_wechat') }}</figcaption>
                </figure>
            @endif
        </div>
    @endif
</div>
