<?php

namespace Wsmallnews\Cms\Settings;

use Spatie\LaravelSettings\Settings;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class GeneralSettings extends Settings
{
    public ?string $wechat = '';

    public ?string $phone = '';

    public ?string $email = '';

    public ?string $address = '';

    public ?string $wechat_qrcode = '';

    public ?string $wechat_official_qrcode = '';

    public ?string $copyright = '';

    public ?string $copytime = '';

    public ?string $beian_no = '';

    public ?string $beian_url = '';

    public static function group(): string
    {
        return 'cms_general';
    }

    public static function repository(): ?string
    {
        return 'database';
    }

    public static function cacheKey(): string
    {
        return static::class . (SupportUtils::isTenancyEnabled() ? '_tenant_' . current_tenant()?->id : '');
    }
}
