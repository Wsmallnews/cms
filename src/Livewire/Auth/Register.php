<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Register extends Base
{
    public function render()
    {
        return view($this->getThemeView('auth.register'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.register'));
    }
}
