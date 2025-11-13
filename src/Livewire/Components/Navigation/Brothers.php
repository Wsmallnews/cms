<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Brothers extends Base
{
    public NavigationModel $navigation;

    public string $wrapperView = 'sn-cms::base.empty-block';

    public function render()
    {
        $brothers = collect([]);
        if ($this->navigation->parent_id) {
            $brothers = $this->navigation
                ->getSiblingsAndSelf();
        }

        return view('sn-cms::livewire.components.navigation.brothers', [
            'brothers' => $brothers,
        ]);
    }
}
