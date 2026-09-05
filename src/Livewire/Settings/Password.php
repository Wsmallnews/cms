<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class Password extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.sidebar.settings_password'))->robots('noindex');
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => Utils::route('profile')],
            ['label' => __('sn-cms::cms.sidebar.settings_password'), 'url' => Utils::route('settings.password')],
        ];

        return view($this->getThemeView('settings.password'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
