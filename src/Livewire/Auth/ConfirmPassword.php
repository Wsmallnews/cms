<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class ConfirmPassword extends Base
{
    #[Title('确认密码')]
    public function render()
    {
        return view($this->getThemeView('auth.confirm-password'), [
        ])->layout(Utils::getLayout());
    }
}
