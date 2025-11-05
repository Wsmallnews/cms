<?php

namespace Wsmallnews\Cms\Livewire\Navigation\Components;

use Wsmallnews\Cms\Livewire\Common\Components\BaseComponent;
use Wsmallnews\Cms\Models\Content as ContentModel;

class Content extends BaseComponent
{
    public ?ContentModel $content = null;

    public string $wrapperView = 'base.block';

    public function render()
    {
        return view('sn-cms::livewire.components.content');
    }
}
