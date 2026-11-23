<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Support\Features\Composition\CompositionRenderer;

class Post extends Base
{
    public string $slug;

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.home'), 'url' => $this->site()?->route('index')],
            ['label' => __('sn-cms::cms.frontend.post_detail'), 'url' => $this->site()?->route('posts.show', $this->slug)],
        ];

        // purpose 侧栏（post-sidebar）：槽位注册的 context 提供者按路由参数解析当前文章，
        // 路由壳不感知 Post 模型；未命中/空编排返回 null，视图回退全宽。
        // 编排分区不传 = 站点 base（各分区详情页共用一份侧栏编排；分区页面要独立编排时显式传）；
        // context 按页面自己的分区解析当前文章
        $sidebar = CompositionRenderer::resolveForPurpose(
            'post-sidebar',
            app(CmsPlugin::class)->getId(),
            params: ['slug' => $this->slug],
            site: $this->site(),
            pageScopeable: $this->getScopeable(),
        );

        return view($this->getThemeView('post.post'), [
            'breadcrumbs' => $breadcrumbs,
            'sidebar' => $sidebar,
        ])->layout($this->getLayout());
    }
}
