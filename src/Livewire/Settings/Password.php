<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Password extends Base
{
    #[Title('修改密码')]
    public function render()
    {
        return view($this->getThemeView('settings.password'), [
        ])->layout(Utils::getLayout());
    }
}
