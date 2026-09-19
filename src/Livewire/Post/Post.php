<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Features\Composition\CompositionRenderer;

class Post extends Base
{
    public string $slug;

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.home'), 'url' => Utils::route('index')],
            ['label' => __('sn-cms::cms.frontend.post_detail'), 'url' => Utils::route('posts.show', $this->slug)],
        ];

        // purpose 侧栏（post-sidebar）：槽位注册的 context 提供者按路由参数解析当前文章，
        // 路由壳不感知 Post 模型；未命中/空编排返回 null，视图回退全宽
        $scopeable = $this->getScopeable();
        $sidebar = CompositionRenderer::resolveForPurpose(
            'post-sidebar',
            app(CmsPlugin::class)->getId(),
            $scopeable['scope_type'],
            $scopeable['scope_id'],
            ['slug' => $this->slug],
        );

        return view($this->getThemeView('post.post'), [
            'breadcrumbs' => $breadcrumbs,
            'sidebar' => $sidebar,
        ])->layout(Utils::getLayout());
    }
}
