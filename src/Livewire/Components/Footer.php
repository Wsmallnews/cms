<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Feed;

class Footer extends Base
{
    use Navigationable;

    public function render()
    {
        $general = app(GeneralSettings::class);

        // 底部导航：独立 scope 的导航树（类型未创建时不发起查询，footer 仅省略导航区）
        $navigations = $this->getScopedQuery()?->normal()->defaultOrder()->get()->toTree() ?? collect([]);

        // 布局自适应：有子级的一级导航 → 分组列；无子级的一级导航 → 快捷链接（平铺）
        $groups = $navigations->filter(fn ($navigation) => $navigation->children->isNotEmpty())->values();
        $flats = $navigations->filter(fn ($navigation) => $navigation->children->isEmpty())->values();

        // 友情链接（启用状态，按 order 排序）
        $links = Utils::getLinkModel()::snScope(...Utils::getScopeable())->normal()->ordered()->get();

        // RSS 订阅入口：只列本模块的流（模块视角隔离——路径前缀部署下 shop 等其他
        // 模块的流不出现），链接指向模块端点 /cms/feed/{name}；feed.enabled 关闭时
        // 前台不渲染（boot 期已决定流与模块端点是否注册，这里是渲染层开关）
        $feeds = Utils::getConfig('feed.enabled', true)
            ? Feed::moduleFeeds(app(CmsPlugin::class)->getId())->map(function (array $feed, string $name): array {
                $title = (string) value($feed['title'] ?? null ?: config('app.name'));

                return [
                    'url' => Feed::feedUrl($name),
                    'title' => $title,
                    'label' => (string) (value($feed['label'] ?? null) ?: $title),
                ];
            })->values()
            : collect([]);

        return view($this->getThemeView('components.footer'), [
            'general' => $general,
            'groups' => $groups,
            'flats' => $flats,
            'links' => $links,
            'feeds' => $feeds,
        ]);
    }

    /**
     * 底部导航的 scopeable（派生约定：模块 scope_type + '-footer'，与后台 FooterNavigationPage 共用）
     */
    public function getScopeable(): array
    {
        return Utils::getFooterScopeable();
    }
}
