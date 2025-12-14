<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Content as ContentModel;

class Content extends Base
{
    public ?ContentModel $content = null;

    public function render()
    {
        return view($this->getThemeView('components.navigation.content'));
    }
}
