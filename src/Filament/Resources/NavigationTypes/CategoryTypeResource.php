<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Concerns\Resource\HasCustomProperties;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\CreateNavigationType;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\EditNavigationType;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\ListNavigationTypes;

final class NavigationTypeResource extends BaseResource
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    public static function getPages(): array
    {
        return [
            'index' => ListNavigationTypes::route('/'),
            'create' => CreateNavigationType::route('/create'),
            'edit' => EditNavigationType::route('/{record}/edit'),
        ];
    }

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
