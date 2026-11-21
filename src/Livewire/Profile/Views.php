<?php

namespace Wsmallnews\Cms\Livewire\Profile;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Support\Facades\Seo;

class Views extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.sidebar.profile_views'))->robots('noindex');
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => $this->site()?->route('profile')],
            ['label' => __('sn-cms::cms.sidebar.profile_views'), 'url' => $this->site()?->route('profile.views')],
        ];

        return view($this->getThemeView('profile.views'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout($this->getLayout());
    }
}
