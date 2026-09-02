<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\Support\Utils;

/**
 * 全局搜索结果页（sn-cms.search.display = 'page' 时由搜索框跳转进入）。
 * 页面布局与路由由 cms 定义，结果区由 support 的核心组件渲染。
 */
class Search extends Base
{
    public function render()
    {
        return view($this->getThemeView('search'), [
            //
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.frontend.search_results'));
    }
}
