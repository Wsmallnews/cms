<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Illuminate\Support\Str;
use Wsmallnews\Cms\Support\Utils;

trait HasThemeView
{
    public ?string $themeView;

    public ?string $bladeThemeView;

    public function getThemeView($name): string
    {
        if (isset($this->themeView) && ! blank($this->themeView)) {
            return $this->themeView;
        }

        $theme = Utils::getTheme();
        $hasViewSpace = Str::contains($theme, 'livewire.');
        $theme = $hasViewSpace ? $theme : "sn-cms::livewire.{$theme}.";

        return $theme . $name;
    }

    public function getBladeThemeView($name): string
    {
        if (isset($this->bladeThemeView) && ! blank($this->bladeThemeView)) {
            return $this->bladeThemeView;
        }

        $theme = Utils::getTheme();
        $hasViewSpace = Str::contains($theme, 'livewire.');

        $theme = $hasViewSpace ? Str::replaceFirst('livewire.', '', $theme) : "sn-cms::{$theme}.";

        return $theme . $name;
    }
}
