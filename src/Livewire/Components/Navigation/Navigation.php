<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Navigation extends Base
{
    use Navigationable;

    public function getNavigations()
    {
        return $this->getScopedQuery()->normal()->defaultOrder()->get()
            ->map(function (NavigationModel $navigation) {
                return $navigation->resolveNavigation($navigation);
            })->toTree();
    }

    public function render()
    {
        return view('sn-cms::livewire.components.navigation.navigation');
    }
}
