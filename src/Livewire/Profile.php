<?php

namespace Wsmallnews\Cms\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Wsmallnews\Cms\Support\Utils;

class Profile extends Base
{
    #[Title('个人中心')]
    public function render()
    {
        return view($this->getThemeView('profile'), [
        ])->layout(Utils::getLayout());
    }
}
