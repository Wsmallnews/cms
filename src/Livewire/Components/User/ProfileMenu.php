<?php

namespace Wsmallnews\Cms\Livewire\Components\User;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;

class ProfileMenu extends Base
{
    public function render()
    {
        $sidebar = [
            [
                'label' => '个人中心',
                'url' => Utils::route('profile'),
            ],
            [
                'label' => '种质申请',
                'url' => Utils::route('user.appraise-applies'),
            ],
            [
                'label' => '修改资料',
                'url' => Utils::route('settings.profile'),
            ],
            [
                'label' => '修改密码',
                'url' => Utils::route('settings.password'),
            ],
        ];

        if (Utils::getConfig('two-factor.enabled', false)) {
            $sidebar[] = [
                'label' => '双因素认证',
                'url' => Utils::route('settings.two-factor'),
            ];
        }

        return view($this->getThemeView('components.user.profile-menu'), [
            'sidebar' => $sidebar,
        ]);
    }
}
