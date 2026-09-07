<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\FilamentNestedset\Livewire\Components\Nestedset;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class Brothers extends Nestedset
{
    use HasThemeView;
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

    public function getNestedset(): Collection
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
                // 侧栏根为二级导航（depth 1），后代递归标注绝对 depth（descendants 关联上 withDepth 以子树为基准，不可用）
                $brother->depth = 1;
                $children = $brother->descendants->toTree();
                $this->markDescendantDepth($children, 2);
                $brother->setRelation('children', $children);

                return $brother;
            });
        }

        return $brothers;
    }

    /**
     * 递归标注后代节点的绝对层级（供手风琴缩进块使用）
     *
     * @param  Collection<int, NavigationModel>  $nodes
     */
    protected function markDescendantDepth(Collection $nodes, int $depth): void
    {
        $nodes->each(function (NavigationModel $node) use ($depth) {
            $node->depth = $depth;
            $this->markDescendantDepth($node->children, $depth + 1);
        });
    }

    public function render(): View
    {
        return view($this->getThemeView('components.navigation.brothers'));
    }
}
