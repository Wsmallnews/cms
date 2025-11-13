<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;

class Navigation extends Base
{
    use Navigationable;

    public function getNavigations()
    {
        return $this->getScopedQuery()->normal()->defaultOrder()
            ->get()->toTree();
    }

    public function render()
    {
        return view('sn-cms::livewire.components.navigation.navigation');
    }
}
