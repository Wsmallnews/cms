<?php

namespace Wsmallnews\Cms\Livewire\Profile;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Views extends Base
{
    #[Title('浏览记录')]
    public function render()
    {
        $breadcrumbs = [
            ['label' => '个人中心', 'url' => Utils::route('profile')],
            ['label' => '浏览记录', 'url' => Utils::route('profile.views')],
        ];

        return view($this->getThemeView('profile.views'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
