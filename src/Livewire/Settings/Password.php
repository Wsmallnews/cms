<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Password extends Base
{
    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => Utils::route('profile')],
            ['label' => __('sn-cms::cms.sidebar.settings_password'), 'url' => Utils::route('settings.password')],
        ];

        return view($this->getThemeView('settings.password'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.sidebar.settings_password'));
    }
}
