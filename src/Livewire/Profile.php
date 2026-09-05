<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class Profile extends Base
{
    public function render()
    {
        Seo::title(__('sn-cms::cms.frontend.profile'))->robots('noindex');
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.profile'), 'url' => Utils::route('profile')],
        ];

        return view($this->getThemeView('profile'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
