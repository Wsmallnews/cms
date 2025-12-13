<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class BrothersBak extends Base
{
    public NavigationModel $navigation;

    public ?NavigationModel $brotherNavigation = null;

    public function render()
    {
        // 固定只显示 二级导航的 兄弟导航 (如果是一级不显示兄弟导航，如果是三级，则显示二级的兄弟)
        if ($this->navigation->depth > 1) {
            // 查找当前导航的所有上级中，层级为 1 的上级
            $parents = $this->navigation->ancestors()->normal()->withDepth()->get();
            $this->brotherNavigation = $parents->firstWhere('depth', 1);
        } elseif ($this->navigation->depth == 1) {
            $this->brotherNavigation = $this->navigation;
        }

        $brothers = collect([]);
        if ($this->brotherNavigation) {
            $brothers = $this->navigation->newScopedQuery()->normal()
                ->with(['children' => function ($query) {
                    $query->normal();
                }])
                ->where($this->navigation->getParentIdName(), '=', $this->brotherNavigation->getParentId())
                ->get();
        }

        return view($this->getView('components.navigation.brothers'), [
            'brothers' => $brothers,
        ]);
    }
}
