<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

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

        return "sn-cms::livewire.{$theme}.{$name}";
    }
}
