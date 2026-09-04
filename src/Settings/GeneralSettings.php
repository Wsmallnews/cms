<?php

namespace Wsmallnews\Cms\Settings;

use Spatie\LaravelSettings\Settings;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class GeneralSettings extends Settings
{
    // ---------- 站点基础 ----------

    public ?string $site_name = '';

    public ?string $site_slogan = '';

    public ?string $logo = '';

    public ?string $favicon = '';

    public ?string $homepage_banner = '';

    public ?string $default_og_image = '';

    // ---------- SEO 默认值 ----------

    public ?string $seo_description = '';

    public ?string $analytics_code = '';

    // ---------- 联系方式 ----------

    public ?string $wechat = '';

    public ?string $phone = '';

    public ?string $email = '';

    public ?string $address = '';

    public ?string $work_time = '';

    public ?string $wechat_qrcode = '';

    public ?string $wechat_official_qrcode = '';

    // ---------- 版权与备案 ----------

    public ?string $copyright = '';

    public ?string $copytime = '';

    public ?string $beian_no = '';

    public ?string $beian_url = '';

    public ?string $beian_police_no = '';

    public ?string $beian_police_url = '';

    public static function group(): string
    {
        return 'cms_general';
    }

    public static function repository(): ?string
    {
        return SupportUtils::isTenancyEnabled() ? 'team_database' : 'database';
    }

    public static function cacheKey(): string
    {
        return static::class . (SupportUtils::isTenancyEnabled() ? '_tenant_' . current_tenant()?->id : '');
    }
}
