<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Profile extends Base
{
    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => Utils::route('profile')],
            ['label' => __('sn-cms::cms.sidebar.settings_profile'), 'url' => Utils::route('settings.profile')],
        ];

        return view($this->getThemeView('settings.profile'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.sidebar.settings_profile'));
    }
}
