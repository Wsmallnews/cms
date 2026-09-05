<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Footer;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Pages\PageConfiguration;

/**
 * 底部导航管理页面（配置解析层）：与头部 NavigationPage 同构——各属性走
 * 「panel_register 注册覆盖 ?? Footer\Base 默认值」，不配置即用派生默认。
 */
final class FooterNavigationPage extends Base
{
    use CanBeConfigured;

    protected static ?string $configurationClass = PageConfiguration::class;

    public static function getScopeType(): string
    {
        return self::getConfigurationValue('scopeType') ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return self::getConfigurationValue('scopeId') ?? parent::getScopeId();
    }

    public static function getLevel(): ?int
    {
        return self::resolveCustomProperty('level') ?? parent::getLevel();
    }

    public static function getModelLabel(): string
    {
        return self::getConfigurationValue('modelLabel') ?? parent::getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return self::getConfigurationValue('pluralModelLabel') ?? parent::getPluralModelLabel();
    }

    public function getTitle(): string
    {
        return self::getConfigurationValue('title') ?? parent::getTitle();
    }

    public static function getNavigationLabel(): string
    {
        return self::getConfigurationValue('navigationLabel') ?? parent::getNavigationLabel();
    }

    public static function getEmptyLabel(): ?string
    {
        return self::getConfigurationValue('emptyLabel') ?? parent::getEmptyLabel();
    }

    public static function getEmptyTipLabel(): ?string
    {
        return self::getConfigurationValue('emptyTipLabel') ?? parent::getEmptyTipLabel();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
