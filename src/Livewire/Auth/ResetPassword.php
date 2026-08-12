<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class ResetPassword extends Base
{
    public function render()
    {
        return view($this->getThemeView('auth.reset-password'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.reset_password'));
    }
}
