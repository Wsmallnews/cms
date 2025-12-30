<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class ForgotPassword extends Base
{
    #[Title('忘记密码')]
    public function render()
    {
        return view($this->getThemeView('auth.forgot-password'), [
        ])->layout(Utils::getLayout());
    }
}
