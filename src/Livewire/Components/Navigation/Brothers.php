<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Support\NavigationContext;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\FilamentNestedset\Livewire\Components\Nestedset;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

/**
 * 同级导航列表：当前请求匹配到二级（或更深）导航时，展示其所在二级分组的兄弟节点。
 *
 * 寻址双通道：调用方显式传 navigation（旧用法）；不传时由 NavigationContext 按当前请求解析。
 * layout 形态（视图内分支渲染，配置见 sn-cms.php navigation 段）：
 * - top = 内容上方一排按钮，深层子级以 hover 下拉树展开（支持多级）
 * - sidebar = 左侧手风琴卡片（支持多级）
 * 无导航上下文 / 匹配到顶级节点时渲染隐藏占位（Livewire 需要根节点），零视觉占位。
 */
class Brothers extends Nestedset
{
    use HasThemeView;
    use Scopeable;

    public ?NavigationModel $navigation = null;

    public ?NavigationModel $brotherNavigation = null;

    public string $layout = '';

    public function mount()
    {
        // 形态未显式指定时读模块配置（调用方一般无需传）
        blank($this->layout) && $this->layout = Utils::navigationConfig('brothers_layout', 'top');

        // 未显式传入时，从当前请求解析导航上下文（一次请求内与其他装饰共享缓存）
        $this->navigation = $this->navigation
            ?? NavigationContext::current($this->scopeType, $this->scopeId);

        if (! $this->navigation) {
            return;
        }

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
                // 分组根为二级导航（depth 1），后代递归标注绝对 depth（descendants 关联上 withDepth 以子树为基准，不可用）
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
     * 递归标注后代节点的绝对层级（供多级缩进使用）
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
        // 单视图内按 layout 分支（top / sidebar）
        return view($this->getThemeView('components.navigation.brothers'));
    }
}
