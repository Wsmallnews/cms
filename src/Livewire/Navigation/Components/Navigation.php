<?php

namespace Wsmallnews\Cms\Livewire\Navigation\Components;

use Wsmallnews\Cms\Livewire\Common\Components\BaseComponent;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Navigation extends BaseComponent
{
    public function getNavigations()
    {
        return NavigationModel::scoped(has_tenancy() ? ['team_id' => current_tenant()->id] : [])
            ->normal()->defaultOrder()->get()
            ->map(function (NavigationModel $navigation) {
                return $navigation->resolveNavigation($navigation);
            })->toTree();
    }

    public function render()
    {
        return view('sn-cms::livewire.navigation.components.navigation');
    }
}
