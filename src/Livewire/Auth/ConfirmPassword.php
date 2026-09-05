<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class ConfirmPassword extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.auth.confirm_password'))->robots('noindex');

        return view($this->getThemeView('auth.confirm-password'), [
        ])->layout(Utils::getLayout());
    }
}
