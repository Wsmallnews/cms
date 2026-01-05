<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class TwoFactor extends Base
{
    #[Title('双因素身份验证')]
    public function render()
    {
        return view($this->getThemeView('settings.two-factor'), [
        ])->layout(Utils::getLayout());
    }
}
