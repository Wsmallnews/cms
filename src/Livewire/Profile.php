<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\Support\Utils;

class Profile extends Base
{
    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.profile'), 'url' => Utils::route('profile')],
        ];

        return view($this->getThemeView('profile'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.frontend.profile'));
    }
}
