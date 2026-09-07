<?php

namespace Wsmallnews\Cms\Filament\Resources\Links;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Wsmallnews\Cms\Filament\Resources\Links\Schemas\LinkForm;
use Wsmallnews\Cms\Filament\Resources\Links\Tables\LinksTable;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Resources\Concerns\Scopeable;

abstract class BaseResource extends Resource
{
    use Scopeable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedLink;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Link;

    protected static ?string $slug = 'links';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    public static function getModel(): string
    {
        return Utils::getLinkModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.link_resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('sn-cms::cms.link_resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? __('sn-cms::cms.link_resource.navigation_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::$navigationGroup ?? __('sn-cms::cms.global_default.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return LinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LinksTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyScopeableToQuery(parent::getEloquentQuery());
    }
}
