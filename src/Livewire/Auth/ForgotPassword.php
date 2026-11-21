<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Support\Facades\Seo;

class ForgotPassword extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.auth.forgot_password'))->robots('noindex');

        return view($this->getThemeView('auth.forgot-password'), [
        ])->layout($this->getLayout());
    }
}
