<?php

namespace Wsmallnews\Cms\Livewire\Navigation;

use Illuminate\Support\Arr;
use Kalnoy\Nestedset\QueryBuilder;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\ContentRegistry;
use Wsmallnews\Cms\Livewire\Common\BasePage;
use Wsmallnews\Cms\Livewire\Navigation\Components\Content;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Navigation extends BasePage
{

    public string $slug;


    /**
     * 先这样解决， queryBuilder 不支持调用 Nestedset 的 scoped 方法
     *
     * @return string|QueryBuilder
     */
    private function getModel(): string|QueryBuilder
    {
        return NavigationModel::scoped(has_tenancy() ? ['team_id' => current_tenant()->id] : []);
    }


    public function render()
    {
        $navigation = $this->getModel()->normal()->where('slug', $this->slug)->firstOrFail();

        // 获取当前导航的所有上级导航，包括自己
        $parents = $this->getModel()->ancestorsAndSelf($navigation->id);

        // 处理上级导航的 url_info
        $prevNavigation = null;
        $parents = $parents->reverse()->map(function (NavigationModel $navigation) use (&$prevNavigation) {
            $navigation =  $navigation->resolveNavigation($navigation);

            // 如果上级没有 url，则使用下级的 url
            $urlInfo = $navigation->url_info; // 先获取数组
            $urlInfo['url'] = $navigation->url_info['url'] ?? ($prevNavigation?->url_info['url'] ?? ''); // 修改副本
            $navigation->url_info = $urlInfo; // 重新赋值

            $prevNavigation = $navigation;      // 保存当前导航
            return $navigation;
        })->reverse();

        // 获取当前导航的所有兄弟导航,包括自己
        $brothers = collect([]);
        if ($navigation->parent_id) {
            $brothers = $navigation->getSiblingsAndSelf();
            $brothers = $brothers->map(function (NavigationModel $navigation) {
                return $navigation->resolveNavigation($navigation);
            });
        }

        if ($navigation->type == NavigationTypeEnum::Content) {
            // 根据当前导航的内容类型，获取导航的设置
            $type = ContentRegistry::getType($navigation->options['type']);

            $components = $type['components'] ?? $type['component'];
            $components = Arr::wrap($components);

            $components = Arr::mapWithKeys($components, function ($component, $key) use ($navigation) {
                $extras = $navigation->options['_extras'] ?? [];          // 额外表单参数，和固定参数合并
                return is_scalar($component) ? [$component => $extras] : [$key => array_merge($component, $extras)];
            });
        } elseif ($navigation->type == NavigationTypeEnum::Page) {
            $components = [
                Content::class => [         // 内容组件
                    'content' => $navigation->content,
                ]
            ];
        }

        return view('sn-cms::livewire.navigation', [
            'navigation' => $navigation,
            'parents' => $parents,
            'brothers' => $brothers,
            'components' => $components ?? [],
        ]);
    }
}
