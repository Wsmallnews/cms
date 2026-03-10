<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes;

use BezhanSalleh\PluginEssentials\Concerns;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\CreateNavigationType;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\EditNavigationType;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\ListNavigationTypes;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

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

    public static function form(Schema $schema): Schema
    {
        return self::getCustomForm($schema) ?: parent::form($schema);
    }

    public static function table(Table $table): Table
    {
        return self::getCustomTable($table) ?: parent::table($table);
    }

    public static function getScopeType(): string
    {
        return self::getCustomScopeType() ?? Utils::getScopeType();
    }

    public static function getScopeId(): int
    {
        return self::getCustomScopeId() ?? Utils::getScopeId();
    }

    public static function getProperties(): array
    {
        return [
            'emptyLabel' => self::getCustomProperty('emptyLabel') ?? null,
        ];
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
