<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Login extends Base
{
    #[Title('登录')]
    public function render()
    {
        return view($this->getThemeView('auth.login'), [
        ])->layout(Utils::getLayout());
    }
}
