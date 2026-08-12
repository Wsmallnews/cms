<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class TwoFactor extends Base
{
    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => Utils::route('profile')],
            ['label' => __('sn-cms::cms.sidebar.settings_two_factor'), 'url' => Utils::route('settings.two-factor')],
        ];

        return view($this->getThemeView('settings.two-factor'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.sidebar.settings_two_factor'));
    }
}
