<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Profile extends Base
{
    #[Title('修改资料')]
    public function render()
    {
        return view($this->getThemeView('settings.profile'), [
        ])->layout(Utils::getLayout());
    }
}
