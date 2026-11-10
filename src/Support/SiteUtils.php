<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Wsmallnews\Member\Models\Member;
use Wsmallnews\Support\Exceptions\InvalidSiteException;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteContext;

/**
 * CMS site-context helpers.
 *
 * Everything here reads the current cms site's options (or frontend auth/theme
 * state bound to it). Registration-period code that owns an explicit site must
 * wrap its work in SiteContext::run($site, ...); there is no ?Site parameter.
 */
final class SiteUtils
{
    /** Current cms site; another module's site is a programming error. */
    public static function getSite(): Site
    {
        return self::site();
    }

    /** Effective option of the current cms site (site options > config options defaults). */
    public static function getConfig(string $name, mixed $default = null): mixed
    {
        return self::site()->getOption($name, $default);
    }

    /** All effective options of the current cms site (config options merged with site options). */
    public static function getConfigs(): array
    {
        return self::site()->getOptions();
    }

    /** Current frontend auth user according to the site's auth_user_type option. */
    public static function getAuthUser(): ?Model
    {
        return self::getConfig('auth_user_type', 'member') === 'member'
            ? self::getAuthMember()
            : self::getUser();
    }

    /** Member resolved by the ResolveMember middleware. */
    public static function getAuthMember(): ?Member
    {
        return current_member();
    }

    /** Frontend user resolved by the site's guard option. */
    public static function getUser(): ?Model
    {
        return Auth::guard(self::getConfig('guard', 'web'))->user();
    }

    /**
     * Comment options of one module for the current site.
     *
     * @param  string  $module  Module name
     * @param  string|null  $key  Option key under the module
     * @param  mixed  $default  Default value if not found
     */
    public static function getCommentConfig(string $module, ?string $key = null, mixed $default = null): mixed
    {
        $modules = self::getConfig('comments', []);
        $moduleConfig = $modules[$module] ?? [];

        return filled($key) ? $moduleConfig[$key] ?? $default : $moduleConfig;
    }

    /** @return array<string, mixed> */
    public static function getThemes(): array
    {
        return (array) self::getConfig('themes', []);
    }

    public static function getDefaultDarkMode(): string
    {
        return self::getConfig('themes.default_dark_mode', 'system');
    }

    public static function hasDarkMode(): bool
    {
        return self::getConfig('themes.dark_mode', false);
    }

    public static function hasDarkModeForced(): bool
    {
        return self::getConfig('themes.dark_mode_forced', false);
    }

    public static function getTheme(): string
    {
        return self::getConfig('themes.theme', 'default');
    }

    public static function getLayout(): string
    {
        return self::getConfig('themes.layout', 'sn-cms::components.layouts.app');
    }

    public static function getPageContainer(): string
    {
        return self::getConfig('themes.page_container', 'sn-cms::container.page');
    }

    /** Full route name of the current cms site, e.g. "posts.show" => "sn-cms.main.posts.show". */
    public static function routeName(string $name): string
    {
        return self::site('routeName')->routeName($name);
    }

    /** Generate one of the current cms site's URLs (tenant parameters come from the context). */
    public static function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return self::site('route')->route($name, $parameters, $absolute);
    }

    /** Current cms site; another module's site is a programming error. */
    private static function site(string $operation = 'config'): Site
    {
        $site = app(SiteContext::class)->site();
        $pluginId = Utils::getPluginId();

        if ($site === null) {
            throw InvalidSiteException::noCurrentSite($operation);
        }

        if ($site->pluginId !== $pluginId) {
            throw InvalidSiteException::configOutsideSite($pluginId, $site->id());
        }

        return $site;
    }
}
