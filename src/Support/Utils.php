<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Filament\Facades\Filament;
use Filament\Panel;
use Wsmallnews\Cms\Exceptions\CmsException;
use Wsmallnews\Cms\Models;

class Utils
{
    public static function getConfig($name = null, $default = null)
    {
        $config = config('sn-cms');

        return $name ? (data_get($config, $name) ?? $default) : $config;
    }

    /**
     * 获取 scopeinfo 参数
     *
     * @throws CmsException
     */
    public static function getScopeable(): array
    {
        $scopeable = self::getConfig('scopeable');
        if (! isset($scopeable['scope_type']) || blank($scopeable['scope_type'])
             || ! isset($scopeable['scope_id']) || blank($scopeable['scope_id'])
        ) {
            throw new CmsException('scopeable配置错误, 请检查 sn-cms.php 配置文件');
        }

        return $scopeable;
    }

    /**
     * 获取 scopeType 参数
     *
     * @throws CmsException
     */
    public static function getScopeType(): string
    {
        return self::getScopeable()['scope_type'];
    }

    /**
     * 获取 scopeId 参数
     *
     * @throws CmsException
     */
    public static function getScopeId(): int
    {
        return self::getScopeable()['scope_id'];
    }

    /**
     * 获取模型
     *
     * @param string $name
     * @return string
     */
    public static function getModel(string $name): string
    {
        $model = self::getConfig('models')[$name];

        if (blank($model)) {
            throw new CmsException("模型 {$name} 不存在");
        }

        return $model;
    }
    /**
     * 获取内容模型
     *
     * @return Models\Content
     */
    public static function getContentModel(): string
    {
        return self::getModel('content');
    }
    /**
     * 获取内容模型
     *
     * @return Models\Navigation
     */
    public static function getNavigationModel(): string
    {
        return self::getModel('navigation');
    }
    /**
     * 获取导航类型模型
     *
     * @return Models\NavigationType
     */
    public static function getNavigationTypeModel(): string
    {
        return self::getModel('navigation_type');
    }
    /**
     * 获取文章模型
     *
     * @return Models\Post
     */
    public static function getPostModel(): string
    {
        return self::getModel('post');
    }
    /**
     * 获取 租户模型
     *
     * @return string|null
     */
    public static function getTenantModel(): ?string
    {
        return self::getConfig('tenant_model') ?? null;
    }

    /**
     * 是否启用了租户
     *
     * @return boolean
     */
    public static function isTenancyEnabled(): bool
    {
        return self::getTenantModel() !== null;
    }


    public static function route($name, $parameters = [], $absolute = true)
    {
        $name = self::getConfig('routes.name', '') . $name;

        return sn_route($name, $parameters, $absolute);
    }
}
