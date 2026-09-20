<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Footer;

use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Pages\PageConfiguration;

/**
 * 底部导航管理页面（配置解析层）：与头部 NavigationPage 同构。
 * 数据分区走 config scopeables 的 footer 实例（panel_register 条目 'scopeable' => 'footer'），
 * 与前台 Footer 组件共用；默认值（图标、slug、层级、标签翻译）在 Footer\Base。
 */
final class FooterNavigationPage extends Base
{
    use CanBeConfigured;

    protected static ?string $configurationClass = PageConfiguration::class;

    public static function getEmptyLabel(): ?string
    {
        return self::resolveCustomProperty('emptyLabel') ?? parent::getEmptyLabel();
    }

    public static function getEmptyTipLabel(): ?string
    {
        return self::resolveCustomProperty('emptyTipLabel') ?? parent::getEmptyTipLabel();
    }
}
