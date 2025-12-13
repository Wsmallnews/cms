<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\FilamentNestedset\Livewire\Components\Nestedset;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class Brothers extends Nestedset
{
    use Scopeable;

    public NavigationModel $navigation;

    public ?NavigationModel $brotherNavigation = null;

    public function mount()
    {
        // 固定只显示 二级导航的 兄弟导航 (如果是一级不显示兄弟导航，如果是三级，则显示二级的兄弟)
        if ($this->navigation->depth > 1) {
            // 查找当前导航的所有上级中，层级为 1 的上级
            $parents = $this->navigation->ancestors()->normal()->withDepth()->get();
            $this->brotherNavigation = $parents->firstWhere('depth', 1);
        } elseif ($this->navigation->depth == 1) {
            $this->brotherNavigation = $this->navigation;
        }
    }

    #[On('sn-filament-nestedset-leaf-click')]
    public function clickNavigation($navigationId) {}

    public function getRecordLabel(Model $record): HtmlString | string
    {
        return $record->name_label;
    }

    public function getHasActive(Model $record): bool
    {
        return $record->has_active;
    }

    public function getNestedset()
    {
        $brothers = collect([]);
        if ($this->brotherNavigation) {
            $brothers = $this->navigation->newScopedQuery()->normal()
                ->with(['descendants' => function ($query) {
                    $query->normal();
                }])
                ->where($this->navigation->getParentIdName(), '=', $this->brotherNavigation->getParentId())
                ->get();

            $brothers = $brothers->map(function ($brother) {
                $children = $brother->descendants->toTree();
                $brother->setRelation('children', $children);

                return $brother;
            });
        }

        return $brothers;
    }

    // public function render()
    // {
    //     return view($this->getView('components.navigation.brothers'));
    // }
}
