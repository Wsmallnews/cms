<?php

namespace Wsmallnews\Cms\Livewire\Profile;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Views extends Base
{
    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.sidebar.profile'), 'url' => Utils::route('profile')],
            ['label' => __('sn-cms::cms.sidebar.profile_views'), 'url' => Utils::route('profile.views')],
        ];

        return view($this->getThemeView('profile.views'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.sidebar.profile_views'));
    }
}
