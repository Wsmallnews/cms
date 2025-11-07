<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Brothers extends Base
{
    public NavigationModel $navigation;

    public string $wrapperView = 'sn-support::base.block';

    public function render()
    {
        $brothers = collect([]);
        if ($this->navigation->parent_id) {
            $brothers = $this->navigation
                ->getSiblingsAndSelf()
                ->map(function (NavigationModel $navigation) {
                    return $navigation->resolveNavigation($navigation);
                });
        }

        return view('sn-cms::livewire.components.navigation.breadcrumb', [
            'brothers' => $brothers
        ]);
    }
}
