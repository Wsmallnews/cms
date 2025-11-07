<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Breadcrumb extends Base
{
    public NavigationModel $navigation;

    public string $wrapperView = 'sn-support::base.block';

    public function render()
    {
        return view('sn-cms::livewire.components.navigation.breadcrumb');
    }
}
