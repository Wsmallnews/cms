<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class VerifyEmail extends Base
{
    #[Title('验证邮箱')]
    public function render()
    {
        return view($this->getThemeView('auth.verify-email'), [
        ])->layout(Utils::getLayout());
    }
}
