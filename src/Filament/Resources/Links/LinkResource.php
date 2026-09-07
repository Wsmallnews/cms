<?php

namespace Wsmallnews\Cms\Filament\Resources\Links;

use Closure;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Links\Pages\CreateLink;
use Wsmallnews\Cms\Filament\Resources\Links\Pages\EditLink;
use Wsmallnews\Cms\Filament\Resources\Links\Pages\ListLinks;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Resources\ResourceConfiguration;

final class LinkResource extends BaseResource
{
    use CanBeConfigured;

    protected static ?string $configurationClass = ResourceConfiguration::class;

    public static function getPages(): array
    {
        return [
            'index' => ListLinks::route('/'),
            'create' => CreateLink::route('/create'),
            'edit' => EditLink::route('/{record}/edit'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $resolveForm = self::resolveCustomProperty('form');

        return $resolveForm instanceof Closure ? $resolveForm($schema, self::class) : parent::form($schema);
    }

    public static function table(Table $table): Table
    {
        $resolveTable = self::resolveCustomProperty('table');

        return $resolveTable instanceof Closure ? $resolveTable($table, self::class) : parent::table($table);
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
