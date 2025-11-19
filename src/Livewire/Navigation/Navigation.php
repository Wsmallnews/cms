<?php

namespace Wsmallnews\Cms\Livewire\Navigation;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Navigation extends Base
{
    public string $slug;

    public function render()
    {
        return view($this->getView('navigation.navigation'))
            ->layout(Utils::getLayout());
    }
}
