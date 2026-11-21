<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Feed;

/**
 * 站点页脚（cms 自用组件）。
 *
 * footer 子 scope 由组件自声明（getScopeSuffix）：站点注册表登记的
 * footer 后缀映射解析（Site::$scopes），未登记时 fail-closed。
 */
class Footer extends Base
{
    use Navigationable;

    /** Footer runs on its own sub-scope, so its navigation type follows it. */
    public static function getScopeSuffix(): string
    {
        return 'footer';
    }

    protected function typeScopeable(): array
    {
        return $this->getScopeable();
    }

    public function render()
    {
        $general = app(GeneralSettings::class);

        // 底部导航：独立 scope 的导航树（类型未创建时不发起查询，footer 仅省略导航区）
        $navigations = $this->getScopedQuery()?->normal()->defaultOrder()->get()->toTree() ?? collect([]);

        // 布局自适应：有子级的一级导航 → 分组列；无子级的一级导航 → 快捷链接（平铺）
        $groups = $navigations->filter(fn ($navigation) => $navigation->children->isNotEmpty())->values();
        $flats = $navigations->filter(fn ($navigation) => $navigation->children->isEmpty())->values();

        // 友情链接（启用状态，按 order 排序；cms main 实例——主 scope，非 footer 子 scope）
        $links = Utils::getLinkModel()::query()
            ->normal()
            ->ordered()
            ->siteContext()
            ->get();

        // RSS 订阅入口：只列本站点所属模块的流（链接指向模块端点 /cms/feed/{name}）；
        // feed.enabled 关闭时前台不渲染（boot 期已决定流与模块端点是否注册，这里是渲染层开关）
        $site = $this->site();

        $feeds = $site && $this->siteOption('feed.enabled', true)
            ? Feed::getFeeds($site)->map(function (array $feed, string $name) use ($site): array {
                $title = (string) value($feed['title'] ?? null ?: config('app.name'));

                return [
                    'url' => Feed::feedUrl($name, $site),
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
}
