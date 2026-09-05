<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Footer;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Pages\PageConfiguration;

/**
 * 底部导航管理页面（配置解析层）：与头部 NavigationPage 同构，仅多两个
 * scopeable 覆盖——CanBeConfigured 的默认回退是插件 scope，底部导航必须
 * 使用 Footer\Base 的派生 scope（模块 scope_type + '-footer'）。
 *
 * 默认值（图标、slug、层级、标签翻译）在 Footer\Base。
 */
final class FooterNavigationPage extends Base
{
    use CanBeConfigured;

    protected static ?string $configurationClass = PageConfiguration::class;

    /**
     * 底部导航的派生 scope 约定（模块 scope_type + '-footer'），与前台 Footer 组件共用
     */
    public static function getScopeType(): string
    {
        return Utils::getFooterScopeType();
    }

    public static function getScopeId(): int
    {
        return Utils::getScopeId();
    }

    public static function getEmptyLabel(): ?string
    {
        return self::resolveCustomProperty('emptyLabel') ?? parent::getEmptyLabel();
    }

    public static function getEmptyTipLabel(): ?string
    {
        return self::resolveCustomProperty('emptyTipLabel') ?? parent::getEmptyTipLabel();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
