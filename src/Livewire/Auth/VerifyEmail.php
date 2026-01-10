<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class VerifyEmail extends Base
{

    public bool $register;

    public function mount()
    {
        $this->register = (bool)request()->query('register', 0);
    }

    #[Title('验证邮箱')]
    public function render()
    {
        return view($this->getThemeView('auth.verify-email'), [
        ])->layout(Utils::getLayout());
    }
}
