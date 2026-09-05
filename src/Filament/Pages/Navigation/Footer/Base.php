<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Footer;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Cms\Filament\Pages\Navigation\Base as NavigationBase;

/**
 * 底部导航管理页面基类：承载底部导航的默认值（派生 scope、两级层级、标签），
 * 与头部 NavigationPage 的分层对齐——共享逻辑在 Navigation\Base，本类只放底部默认，
 * 配置解析层（CanBeConfigured）在 FooterNavigationPage。
 *
 * 需要定制底部导航页面的场景可继承本类覆盖默认值。
 */
abstract class Base extends NavigationBase
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBars3BottomRight;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3BottomRight;

    protected static ?string $slug = 'footer-navigations';

    protected static ?int $navigationSort = 2;

    /**
     * 底部导航默认两级（超出层级的创建/拖拽会被拒绝）
     */
    protected static ?int $level = 2;

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.footer_navigation_page.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('sn-cms::cms.footer_navigation_page.plural_model_label');
    }

    public function getTitle(): string
    {
        return static::$title ?? __('sn-cms::cms.footer_navigation_page.title');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? __('sn-cms::cms.footer_navigation_page.navigation_label');
    }

    public static function getEmptyLabel(): ?string
    {
        return static::$emptyLabel ?? __('sn-cms::cms.footer_navigation_page.empty_label');
    }

    public static function getEmptyTipLabel(): ?string
    {
        return static::$emptyTipLabel ?? __('sn-cms::cms.footer_navigation_page.empty_tip_label');
    }
}
