<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;
use Wsmallnews\Support\Models\Page;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class Index extends Base
{
    public function render()
    {
        // 首页不声明页面标题（渲染时仅输出站点名），声明 WebSite 结构化数据
        Seo::website();

        return view($this->getThemeView('index'), [
            'homePage' => $this->getHomePage(),
            'module' => app(CmsPlugin::class)->getId(),
        ])->layout(Utils::getLayout());
    }

    /**
     * 首页解析链：is_home 导航节点 → page_id → Page（内容双通道在 Page 侧：编排或自有内容）
     *
     * 导航实体按模块归属（$this->getScopeable()，经 cms Base 解析模块主 scope）查询；
     * 链路任一环缺失返回 null，首页回退空状态提示
     */
    protected function getHomePage(): ?Page
    {
        $navigationModel = new (Utils::getNavigationModel());

        $home = $navigationModel->query()
            ->normal()
            ->snScope(...$this->getScopeable())
            ->where('options->is_home', true)
            ->first();

        if (blank($home?->page_id)) {
            return null;
        }

        return SupportUtils::getPageModel()::query()
            ->published()
            ->snScope(...$this->getScopeable())
            ->with('content')
            ->find($home->page_id);
    }
}
