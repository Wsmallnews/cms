<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Wsmallnews\Support\Exceptions\InvalidSiteException;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteContext;

/**
 * CMS site-context helpers.
 *
 * Only for code without a Livewire component instance (layouts, Blade container
 * components, middleware, models, registration closures). Inside a component or
 * its content view use ScopedBySite / HasCmsConfig instead.
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
