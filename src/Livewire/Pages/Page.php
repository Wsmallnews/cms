<?php

namespace Wsmallnews\Cms\Livewire\Pages;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

/**
 * 站点页面路由页（/cms/pages/{slug}）：薄壳，页面解析与渲染由 support 的
 * sn-support::components.page.page 内容组件承载（查询/SEO/双通道渲染）。
 */
class Page extends Base
{
    public ?string $slug = null;

    public function render()
    {
        return view($this->getThemeView('pages.page'), [
            'module' => app(CmsPlugin::class)->getId(),
        ])->layout(Utils::getLayout());
    }
}
