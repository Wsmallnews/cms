<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Wsmallnews\Cms\Exceptions\CmsException;
use Wsmallnews\Cms\Models;
use Wsmallnews\Support\Models\Tag as SupportTagModel;

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
     */
    public static function getModel(string $name, bool $shouldException = true): ?string
    {
        $model = self::getConfig('models')[$name] ?? null;

        if (blank($model) && $shouldException) {
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
     * 获取文章模型
     *
     * @return Models\Post
     */
    public static function getTagModel(): string
    {
        return self::getModel('tag', false) ?? SupportTagModel::class;
    }

    /**
     * 获取文件目录
     *
     * @param  string|null  $type  目录类型
     * @return string
     */
    public static function getFileDirectory($type = null)
    {
        return self::getConfig('file_directory', 'sn/cms/') . ($type ? $type . '/' : '') . date('Ymd');
    }

    /**
     * 获取主题配置信息
     */
    public static function getThemes(): array
    {
        return self::getConfig('themes');
    }

    /**
     * 获取当前主题
     */
    public static function getTheme(): string
    {
        return self::getConfig('themes.theme', 'default');
    }

    /**
     * 获取当前布局
     */
    public static function getLayout(): string
    {
        return self::getConfig('themes.layout', 'sn-cms::components.layouts.app');
    }

    /**
     * 获取指定容器
     */
    public static function getThemeContainer($name): string
    {
        return self::getConfig("themes.containers.{$name}", null);
    }

    /**
     * cms 内部路由处理
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    public static function route($name, $parameters = [], $absolute = true): string
    {
        $name = self::getConfig('routes.name', '') . $name;

        return sn_route($name, $parameters, $absolute);
    }
}
