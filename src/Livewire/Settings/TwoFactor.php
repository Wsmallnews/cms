<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Support\Facades\Seo;

class TwoFactor extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.sidebar.settings_two_factor'))->robots('noindex');
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => $this->site()?->route('profile')],
            ['label' => __('sn-cms::cms.sidebar.settings_two_factor'), 'url' => $this->site()?->route('settings.two-factor')],
        ];

        return view($this->getThemeView('settings.two-factor'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout($this->getLayout());
    }
}
