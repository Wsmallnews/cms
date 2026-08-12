<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class ForgotPassword extends Base
{
    public function render()
    {
        return view($this->getThemeView('auth.forgot-password'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.forgot_password'));
    }
}
