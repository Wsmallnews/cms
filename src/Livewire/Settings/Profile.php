<?php

namespace Wsmallnews\Cms\Livewire\Settings;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Support\Facades\Seo;

class Profile extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.sidebar.settings_profile'))->robots('noindex');
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => $this->site()?->route('profile')],
            ['label' => __('sn-cms::cms.sidebar.settings_profile'), 'url' => $this->site()?->route('settings.profile')],
        ];

        return view($this->getThemeView('settings.profile'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout($this->getLayout());
    }
}
