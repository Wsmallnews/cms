<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

final class NavigationPage extends Base
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    public static function getScopeType(): string
    {
        return self::getCustomScopeType() ?? Utils::getScopeType();
    }

    public static function getScopeId(): int
    {
        return self::getCustomScopeId() ?? Utils::getScopeId();
    }

    public static function getLevel(): ?int
    {
        return self::getCustomProperty('level') ?? parent::getLevel();
    }

    public static function getCanManage(): bool
    {
        return self::getCustomProperty('canManage', false);
    }

    public static function getEmptyLabel(): ?string
    {
        return self::getCustomProperty('emptyLabel') ?? parent::getEmptyLabel();
    }

    public static function getEmptyTipLabel(): ?string
    {
        return self::getCustomProperty('emptyTipLabel') ?? parent::getEmptyTipLabel();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
