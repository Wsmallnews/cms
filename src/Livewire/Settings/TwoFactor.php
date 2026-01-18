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
        $breadcrumbs = [
            ['label' => '个人中心', 'url' => Utils::route('profile')],
            ['label' => '双因素身份验证', 'url' => Utils::route('settings.two-factor')],
        ];

        return view($this->getThemeView('settings.two-factor'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
