<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\Navigation as NavigationWidget;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Schemas\NavigationTypeForm;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Tables\NavigationTypesTable;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Resources\Concerns\Scopeable;

abstract class BaseResource extends Resource
{
    use Scopeable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3BottomRight;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Bars3BottomRight;

    protected static ?string $slug = 'navigation-types';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getModel(): string
    {
        return Utils::getNavigationTypeModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.navigation_type_resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('sn-cms::cms.navigation_type_resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? __('sn-cms::cms.navigation_type_resource.navigation_label');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::$navigationGroup ?? __('sn-cms::cms.global_default.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return NavigationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NavigationTypesTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            NavigationWidget::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyScopeableToQuery(parent::getEloquentQuery())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
