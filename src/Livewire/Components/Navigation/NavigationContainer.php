<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;
use Wsmallnews\Support\Features\Composition\CompositionRenderer;

class NavigationContainer extends Base
{
    use Navigationable;

    public string $slug;

    public function render()
    {
        $navigationModel = new (Utils::getNavigationModel());

        // 导航类型不存在时导航页本身不可达，直接 404
        if (! $this->hasNavigationType()) {
            abort(404);
        }

        $navigation = $this->getScopedQuery()
            ->normal()
            ->withDepth()
            ->where($navigationModel->getRouteKeyName(), $this->slug)
            ->firstOrFail();

        // 导航页 SEO：以导航名称为标题、导航描述为页面描述
        Seo::title($navigation->name)->description($navigation->description);

        // 统一行式渲染：内容类型解析引用的编排；单页类型映射为一个通栏行
        if ($navigation->type == NavigationTypeEnum::Content) {
            // 内容类型 = 引用内容编排（Composition）；内容实体按模块归属查询，不用页面实例 scope。
            // 引用失效（编排被删 / 未发布 / 未绑定）时渲染空内容区
            $compositionId = $navigation->options['composition_id'] ?? null;

            $composition = filled($compositionId)
                ? Utils::getCompositionModel()::query()
                    ->published()
                    ->snScope(...$this->getScopeable())
                    ->find($compositionId)
                : null;

            $rows = $composition
                ? CompositionRenderer::resolveRows($composition->components, app(CmsPlugin::class)->getId())
                : [];
        } elseif ($navigation->type == NavigationTypeEnum::Page) {
            $rows = [
                [
                    'layout' => CompositionRenderer::LAYOUT_FULL,
                    'left' => [
                        [
                            'component_name' => Content::class,
                            'extras' => ['content' => $navigation->content],
                        ],
                    ],
                    'right' => [],
                ],
            ];
        }

        return view($this->getThemeView('components.navigation.navigation-container'), [
            'navigation' => $navigation,
            'rows' => $rows ?? [],
        ]);
    }
}
