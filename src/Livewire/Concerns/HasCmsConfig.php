<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

/**
 * cms's config sections on the current site (navigation / themes / comments / search).
 *
 * Site options win — they already fall back to the site's own module defaults,
 * so a shop site reads `sn-shop.options.*`. When neither declares a key, the cms
 * defaults apply: these sections are cms's structure, so its package config is
 * the last resort wherever a component is reused.
 */
trait HasCmsConfig
{
    /** Read one key of the navigation section. */
    public function navigationOption(string $key, mixed $default = null): mixed
    {
        return $this->siteOption("navigation.{$key}", $default);
    }

    /** Read one key of the theme section. */
    public function themeOption(string $key, mixed $default = null): mixed
    {
        return $this->siteOption("themes.{$key}", $default);
    }

    /** Read one key of the search section. */
    public function searchOption(string $key, mixed $default = null): mixed
    {
        return $this->siteOption("search.{$key}", $default);
    }

    /** Frontend layout of the current site. */
    public function getLayout(): string
    {
        return $this->siteOption('themes.layout', 'sn-cms::components.layouts.app');
    }

    /** Page container component of the current site. */
    public function getPageContainer(): string
    {
        return $this->siteOption('themes.page_container', 'sn-cms::container.page');
    }

    /** Read one key of a module's comments section (e.g. comments.post.enable). */
    public function commentOption(string $module, ?string $key = null, mixed $default = null): mixed
    {
        return $this->siteOption('comments.' . $module . ($key ? ".{$key}" : ''), $default);
    }

    /** Whether desktop parent items are clickable (hover cascade only: jump to the first available leaf). */
    public function isDesktopParentClickable(): bool
    {
        return $this->navigationOption('desktop_submenu_style', 'cascade') === 'cascade'
            && $this->navigationOption('desktop_submenu_trigger', 'hover') === 'hover'
            && (bool) $this->navigationOption('parent_clickable', true);
    }
}
