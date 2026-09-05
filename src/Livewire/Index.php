<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class Index extends Base
{
    public function render()
    {
        // 首页不声明页面标题（渲染时仅输出站点名），声明 WebSite 结构化数据
        Seo::website();

        return view($this->getThemeView('index'), [
        ])->layout(Utils::getLayout());
    }
}
