<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Wsmallnews\Cms\Exceptions\CmsException;
use Wsmallnews\Member\Models\Member;
use Wsmallnews\Support\Data\ScopeableContext;
use Wsmallnews\Support\Exceptions\InvalidScopeException;
use Wsmallnews\Support\Models\Tag as SupportTagModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

/**
 * Utility class for CMS package configuration and helpers.
 */
class Utils
{
    /**
     * Get configuration value.
     *
     * @param  string|null  $name  Configuration key (dot notation)
     * @param  mixed  $default  Default value if not found
     */
    public static function getConfig(?string $name = null, mixed $default = null): mixed
    {
        $config = config('sn-cms');

        return $name ? (data_get($config, $name) ?? $default) : $config;
    }

    /**
     * Get scopeable configuration as ScopeableContext object.
     *
     * @throws CmsException
     */
    public static function getScopeableContext(): ScopeableContext
    {
        try {
            return SupportUtils::getScopeFromConfig('sn-cms.scopeable');
        } catch (InvalidScopeException $e) {
            throw new CmsException('Scopeable configuration error. ' . $e->getMessage());
        }
    }

    /**
     * Get scopeable array (legacy method for backward compatibility).
     *
     * @return array{scope_type: string, scope_id: int}
     *
     * @throws CmsException
     */
    public static function getScopeable(): array
    {
        return self::getScopeableContext()->toArray();
    }

    /**
     * Get scope type.
     *
     * @throws CmsException
     */
    public static function getScopeType(): string
    {
        return self::getScopeableContext()->scopeType;
    }

    /**
     * Get scope ID.
     *
     * @throws CmsException
     */
    public static function getScopeId(): int
    {
        return self::getScopeableContext()->scopeId;
    }

    /**
     * 当前 cms 用户端认证用户
     */
    public static function getUser(): ?Model
    {
        return Auth::guard(self::getConfig('guard', 'web'))->user();
    }

    /**
     * 获取当前请求中的 Member（由 ResolveMember 中间件设置）
     */
    public static function getAuthMember(): ?Member
    {
        return current_member();
    }

    /**
     * 获取当前请求中的认证用户（根据 auth_user 配置）
     */
    public static function getAuthUser(): ?Model
    {
        return self::getConfig('auth_user_type', 'member') === 'member' ? static::getAuthMember() : static::getUser();
    }

    /**
     * Get panel register config.
     *
     * @param  string|null  $type  Register type (pages, resources, global_default) or null for all
     */
    public static function getPanelRegister(?string $type = null): mixed
    {
        if (blank($type)) {
            return self::getConfig('panel_register', null);
        }

        return self::getConfig("panel_register.$type", null);
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
     * Get tag model class.
     *
     * @return string Models\Post
     */
    public static function getTagModel(): string
    {
        return self::getModel('tag', false) ?? SupportTagModel::class;
    }

    /**
     * Get model class by name.
     */
    public static function getFlags(): ?array
    {
        return self::getConfig('flags', []);
    }

    /**
     * 获取 module 是否支持 评论
     *
     * @param  string  $module  Module name
     * @param  string|null  $key  Configuration key
     * @param  mixed  $default  Default value if not found
     */
    public static function commentConfig(string $module, ?string $key = null, $default = null): mixed
    {
        $modules = self::getConfig('comments', []);
        $moduleConfig = $modules[$module] ?? [];

        return filled($key) ? $moduleConfig[$key] ?? $default : $moduleConfig;
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

    /**
     * Get theme configuration.
     */
    public static function getThemes(): array
    {
        return self::getConfig('themes');
    }

    /**
     * Get default dark mode setting.
     */
    public static function getDefaultDarkMode(): string
    {
        return self::getConfig('themes.default_dark_mode', 'system');
    }

    /**
     * Check if dark mode is enabled.
     */
    public static function hasDarkMode(): bool
    {
        return self::getConfig('themes.dark_mode', false);
    }

    /**
     * Check if dark mode is forced.
     */
    public static function hasDarkModeForced(): bool
    {
        return self::getConfig('themes.dark_mode_forced', false);
    }

    /**
     * Get current theme name.
     */
    public static function getTheme(): string
    {
        return self::getConfig('themes.theme', 'default');
    }

    /**
     * Get layout view path.
     */
    public static function getLayout(): string
    {
        return self::getConfig('themes.layout', 'sn-cms::components.layouts.app');
    }

    /**
     * Get page container view path.
     */
    public static function getPageContainer(): string
    {
        return self::getConfig('themes.page_container', 'sn-cms::container.page');
    }

    /**
     * Generate CMS route with configured prefix.
     *
     * @param  string  $name  Route name
     * @param  mixed  $parameters  Route parameters
     * @param  bool  $absolute  Generate absolute URL
     */
    public static function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $name = self::getConfig('routes.name', '') . $name;

        return sn_route($name, $parameters, $absolute);
    }
}
