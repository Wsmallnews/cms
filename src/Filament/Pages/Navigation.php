<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Pages\Navigation\Base as BaseNavigationPage;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

final class Navigation extends BaseNavigationPage
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    public ?int $level = 2;

    public static function getScopeType(): string
    {
        return self::getCustomProperty('scopeType') ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return self::getCustomProperty('scopeId') ?? parent::getScopeId();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
