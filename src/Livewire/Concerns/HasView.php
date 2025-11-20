<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Illuminate\Support\Str;
use Wsmallnews\Cms\Support\Utils;

trait HasView
{
    public ?string $view;

    public function getView($name): string
    {
        if (isset($this->view) && ! blank($this->view)) {
            return $this->view;
        }

        $theme = Utils::getTheme();
        $hasViewSpace = Str::contains($theme, 'livewire.');
        $theme = $hasViewSpace ? $theme : "sn-cms::livewire.{$theme}.";

        return $theme . $name;
    }
}
