<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class ResetPassword extends Base
{
    #[Title('重置密码')]
    public function render()
    {
        return view($this->getThemeView('auth.reset-password'), [
        ])->layout(Utils::getLayout());
    }
}
