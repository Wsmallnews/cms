<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class Login extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.auth.login'))->robots('noindex');
        return view($this->getThemeView('auth.login'), [
        ])->layout(Utils::getLayout());
    }
}
