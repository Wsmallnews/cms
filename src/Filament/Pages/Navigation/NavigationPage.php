<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Pages\PageConfiguration;

final class NavigationPage extends Base
{
    use CanBeConfigured;

    protected static ?string $configurationClass = PageConfiguration::class;

    public static function getLevel(): ?int
    {
        if (self::getCanManage()) {
            // 可管理导航类型，则使用父级 getLevel 方法
            return parent::getLevel();
        }

        return self::resolveCustomProperty('level') ?? parent::getLevel();
    }

    public static function getCanManage(): bool
    {
        return self::resolveCustomProperty('canManage') ?? false;
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
