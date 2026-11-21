<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Illuminate\Support\Str;

trait HasThemeView
{
    public ?string $themeView;

    public ?string $bladeThemeView;

    public function getThemeView($name): string
    {
        if (isset($this->themeView) && ! blank($this->themeView)) {
            return $this->themeView;
        }

        $theme = $this->resolveTheme();
        $hasViewSpace = Str::contains($theme, 'livewire.');
        $theme = $hasViewSpace ? $theme : "sn-cms::livewire.{$theme}.";

        return $theme . $name;
    }

    public function getBladeThemeView($name): string
    {
        if (isset($this->bladeThemeView) && ! blank($this->bladeThemeView)) {
            return $this->bladeThemeView;
        }

        $theme = $this->resolveTheme();
        $hasViewSpace = Str::contains($theme, 'livewire.');

        $theme = $hasViewSpace ? Str::replaceFirst('livewire.', '', $theme) : "sn-cms::{$theme}.";

        return $theme . $name;
    }

    /**
     * Theme of the current site; the cms package config is the last resort when
     * the site (and its own module) declares nothing.
     */
    protected function resolveTheme(): string
    {
        return (string) $this->themeOption('theme', 'default');
    }
}
