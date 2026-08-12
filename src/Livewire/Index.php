<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\Support\Utils;

class Index extends Base
{
    public function render()
    {
        return view($this->getThemeView('index'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.index'));
    }
}
