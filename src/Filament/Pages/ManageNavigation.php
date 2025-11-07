<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Pages\Navigation\ManageBase as ManageBaseNavigationPage;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

final class ManageNavigation extends ManageBaseNavigationPage
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    public static function getScopeType(): string
    {
        return Utils::getScopeable()['scope_type'] ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return Utils::getScopeable()['scope_id'] ?? parent::getScopeId();
    }

    public function getEmptyLabel(): ?string
    {
        return self::getCustomProperty('emptyLabel');
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
