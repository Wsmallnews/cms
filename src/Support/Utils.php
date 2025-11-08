<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Filament\Facades\Filament;
use Filament\Panel;
use Wsmallnews\Cms\Exceptions\CmsException;

class Utils
{
    public static function getConfig($name = null)
    {
        $config = config('sn-cms');

        return $name ? ($config[$name] ?? null) : $config;
    }

    /**
     * 获取 scopeinfo 参数
     * 
     * @return array
     * @throws CmsException
     */
    public static function getScopeable(): array
    {
        $scopeable = self::getConfig('scopeable');
        if (!isset($scopeable['scope_type']) || blank($scopeable['scope_type'])
             || !isset($scopeable['scope_id']) || blank($scopeable['scope_id'])
        ) {
            throw new CmsException('scopeable配置错误, 请检查 sn-cms.php 配置文件');
        }

        return $scopeable;
    }

    /**
     * 获取 scopeType 参数
     * 
     * @return string
     * @throws CmsException
     */
    public static function getScopeType(): string
    {
        return self::getScopeable()['scope_type'];
    }

    /**
     * 获取 scopeId 参数
     * 
     * @return 
     * @throws CmsException
     */
    public static function getScopeId(): int
    {
        return self::getScopeable()['scope_id'];
    }

    public static function currentPanel(): ?Panel
    {
        return Filament::getCurrentOrDefaultPanel();
    }

    // public static function getModel($name)
    // {
    //     return self::getConfig('models')[$name] ?? \Wsmallnews\Cms\Models\Content::class;
    // }

    public static function isTenancyEnabled(): bool
    {
        return self::currentPanel()?->hasTenancy() ?? false;
    }

    public static function getTenantModel(): ?string
    {
        return self::isTenancyEnabled() ? self::currentPanel()?->getTenantModel() : null;
    }
}
