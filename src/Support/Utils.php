<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Contracts\PostFlagContract;
use Wsmallnews\Cms\Enums\PostFlag;
use Wsmallnews\Cms\Exceptions\CmsException;
use Wsmallnews\Support\Models\Tag as SupportTagModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

/**
 * Utility class for CMS package configuration and helpers.
 */
class Utils
{
    /** Module plugin id; also the config root (config/{pluginId}.php). */
    public static function getPluginId(): string
    {
        return app(CmsPlugin::class)->getId();
    }

    /**
     * Read module config by its full path, e.g. 'options.feed.limit' or
     * 'contents.post.types'. Never reads site options — use
     * {@see SiteUtils::getConfig()} for the current site.
     *
     * @param  string  $name  Full config path under the module root
     * @param  mixed  $default  Default value if not found
     */
    public static function getConfig(string $name, mixed $default = null): mixed
    {
        return config(self::getPluginId() . ".{$name}", $default);
    }

    /**
     * Get model class by name.
     *
     * @param  string  $name  Model name (e.g., 'post', 'navigation')
     * @param  bool  $shouldException  Whether to throw exception if not found
     *
     * @throws CmsException
     */
    public static function getModel(string $name, bool $shouldException = true): ?string
    {
        $model = self::getConfig('models')[$name] ?? null;

        if (blank($model) && $shouldException) {
            throw new CmsException("Model {$name} not found.");
        }

        return $model;
    }

    /**
     * Get navigation model class.
     *
     * @return string Models\Navigation
     */
    public static function getNavigationModel(): string
    {
        return self::getModel('navigation');
    }

    /**
     * Get navigation type model class.
     *
     * @return string Models\NavigationType
     */
    public static function getNavigationTypeModel(): string
    {
        return self::getModel('navigation_type');
    }

    /**
     * Get post model class.
     *
     * @return string Models\Post
     */
    public static function getPostModel(): string
    {
        return self::getModel('post');
    }

    /**
     * Get link (friend link) model class.
     *
     * @return string Models\Link
     */
    public static function getLinkModel(): string
    {
        return self::getModel('link');
    }

    /**
     * Get composition model class（实体在 support 包，经 sn-support.models.composition 可替换）.
     *
     * @return string \Wsmallnews\Support\Models\Composition
     */
    public static function getCompositionModel(): string
    {
        return SupportUtils::getCompositionModel();
    }

    /**
     * Get tag model class.
     *
     * @return string Models\Post
     */
    public static function getTagModel(): string
    {
        return self::getModel('tag', false) ?? SupportTagModel::class;
    }

    /**
     * 获取 flag enum 类（可经 sn-cms.enums.post_flag 配置替换，须实现 PostFlagContract）。
     *
     * @return class-string<PostFlagContract>
     */
    public static function getFlagEnum(): string
    {
        return self::getConfig('enums.post_flag', PostFlag::class);
    }

    /**
     * Get file directory path with optional type and date.
     *
     * @param  string|null  $type  Directory type
     */
    public static function getFileDirectory(?string $type = null): string
    {
        return self::getConfig('file_directory', 'sn/cms/') . ($type ? $type . '/' : '') . date('Ymd');
    }
}
