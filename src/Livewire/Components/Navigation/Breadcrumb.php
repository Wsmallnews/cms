<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;

class Breadcrumb extends Base
{
    public NavigationModel $navigation;

    public string $wrapperView = 'sn-support::base.empty-block';

    public function render()
    {
        // 获取当前导航的所有上级导航，包括自己
        $parents = $this->navigation->ancestors;
        $parents = $parents->push($this->navigation);       // 追加自己

        // 处理上级导航的 url_info
        $prevNavigation = null;
        $parents = $parents->reverse()->map(function (NavigationModel $navigation) use (&$prevNavigation) {
            $navigation = $navigation->resolveNavigation($navigation);

            // 如果上级没有 url，则使用下级的 url
            $urlInfo = $navigation->url_info; // 先获取数组
            $urlInfo['url'] = $navigation->url_info['url'] ?? ($prevNavigation?->url_info['url'] ?? ''); // 修改副本
            $navigation->url_info = $urlInfo; // 重新赋值

            $prevNavigation = $navigation;      // 保存当前导航

            return $navigation;
        })->reverse();

        $breadcrumbs = [];
        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'url' => $parent->url_info['url'],
                'label' => $parent->name_label,
            ];
        }

        return view('sn-cms::livewire.components.navigation.breadcrumb', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
