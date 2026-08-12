<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Login extends Base
{
    public function render()
    {
        return view($this->getThemeView('auth.login'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.login'));
    }
}
