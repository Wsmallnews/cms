<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class ConfirmPassword extends Base
{
    public function render()
    {
        return view($this->getThemeView('auth.confirm-password'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.confirm_password'));
    }
}
