<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;
use Wsmallnews\Support\Models\Content as ContentModel;

class Content extends Base
{
    use CanBeContained;

    public ?ContentModel $content = null;

    public function render()
    {
        return view($this->getThemeView('components.navigation.content'));
    }
}
