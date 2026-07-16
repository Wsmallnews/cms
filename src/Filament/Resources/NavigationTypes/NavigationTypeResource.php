<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes;

use Closure;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\CreateNavigationType;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\EditNavigationType;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages\ListNavigationTypes;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Resources\ResourceConfiguration;

final class NavigationTypeResource extends BaseResource
{
    use CanBeConfigured;

    protected static ?string $configurationClass = ResourceConfiguration::class;

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
        $resolveForm = self::resolveCustomProperty('form');
        $schema = $resolveForm instanceof Closure ? $resolveForm($schema, self::class) : null;

        return $schema ?? parent::form($schema);
    }

    public static function table(Table $table): Table
    {
        $resolveTable = self::resolveCustomProperty('table');
        $table = $resolveTable instanceof Closure ? $resolveTable($table, self::class) : null;

        return $table ?? parent::table($table);
    }

    public static function getProperties(): array
    {
        return [
            'emptyLabel' => self::resolveCustomProperty('emptyLabel'),
        ];
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
