<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class ResetPassword extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.auth.reset_password'))->robots('noindex');

        return view($this->getThemeView('auth.reset-password'), [
        ])->layout(Utils::getLayout());
    }
}
