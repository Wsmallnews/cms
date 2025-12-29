<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Register extends Base
{
    #[Title('注册')]
    public function render()
    {
        return view($this->getThemeView('auth.register'), [
        ])->layout(Utils::getLayout());
    }
}
