<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Support\Utils;

class Profile extends Base
{
    #[Title('个人中心')]
    public function render()
    {

        $sidebar = [
            [
                'label' => '个人中心',
                'url' => '#',
            ],
            [
                'label' => '其他列表',
                'url' => '#',
            ],
        ];

        return view($this->getThemeView('profile'), [
            'sidebar' => $sidebar,
        ])->layout(Utils::getLayout());
    }
}
