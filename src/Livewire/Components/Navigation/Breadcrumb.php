<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Support\Utils;

class Breadcrumb extends Base
{
    public NavigationModel $navigation;

    public function render()
    {
        // 获取当前导航的所有上级导航，包括自己
        $parents = $this->navigation->ancestors()->normal()->get();
        $parents = $parents->push($this->navigation);       // 追加自己

        // 处理上级导航的 url_info
        $prevNavigation = null;
        $breadcrumbs = $parents->reverse()->map(function (NavigationModel $navigation) use (&$prevNavigation) {
            // 如果上级没有 url，则使用下级的 url
            $urlInfo = $navigation->url_info; // 先获取数组
            $urlInfo['url'] = $navigation->url_info['url'] ?? ($prevNavigation?->url_info['url'] ?? ''); // 修改副本

            $prevNavigation = $navigation;      // 保存当前导航，后续循环使用

            return [
                'url' => $urlInfo['url'],
                'label' => $navigation->name_label,
            ];
        })->reverse();

        // 添加首页
        $breadcrumbs->prepend([
            'url' => Utils::route('index'),
            'label' => '首页',
        ]);

        return view($this->getView('components.navigation.breadcrumb'), [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
