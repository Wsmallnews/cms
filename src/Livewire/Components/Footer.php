<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Cms\Support\Utils;

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

        return view($this->getThemeView('components.footer'), [
            'general' => $general,
            'groups' => $groups,
            'flats' => $flats,
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
