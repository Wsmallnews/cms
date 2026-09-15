<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;
use Wsmallnews\Support\Features\Composition\CompositionRenderer;

class Index extends Base
{
    public function render()
    {
        // 首页不声明页面标题（渲染时仅输出站点名），声明 WebSite 结构化数据
        Seo::website();

        return view($this->getThemeView('index'), [
            'compositionRows' => $this->getHomeCompositionRows(),
        ])->layout(Utils::getLayout());
    }

    /**
     * 首页编排解析链：is_home 导航节点（content 类型）→ 绑定的 Composition（published）→ 可渲染行
     *
     * 内容实体一律按模块归属（$this->getScopeable()，经 cms Base 解析模块主 scope）查询，不用页面实例 scope；
     * 链路任一环缺失返回 null，首页回退空状态提示
     */
    protected function getHomeCompositionRows(): ?array
    {
        $navigationModel = new (Utils::getNavigationModel());

        $home = $navigationModel->query()
            ->normal()
            ->snScope(...$this->getScopeable())
            ->where('type', NavigationTypeEnum::Content)
            ->where('options->is_home', true)
            ->first();

        $compositionId = $home?->options['composition_id'] ?? null;
        if (blank($compositionId)) {
            return null;
        }

        $composition = Utils::getCompositionModel()::query()
            ->published()
            ->snScope(...$this->getScopeable())
            ->find($compositionId);

        if (! $composition) {
            return null;
        }

        return CompositionRenderer::resolveRows($composition->components, app(CmsPlugin::class)->getId());
    }
}
