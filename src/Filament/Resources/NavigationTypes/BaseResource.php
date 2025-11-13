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
use Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\NavigationManage as NavigationManageWidgets;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Schemas\NavigationTypeForm;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Tables\NavigationTypesTable;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Resources\Concerns\Scopeable;

abstract class BaseResource extends Resource
{
    use Scopeable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Bars3;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3;

    protected static ?string $navigationLabel = '导航类型';

    protected static string | UnitEnum | null $navigationGroup = '导航管理';

    protected static ?string $slug = 'navigation-types';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = '导航类型';

    protected static ?string $pluralModelLabel = '导航类型';

    protected static ?int $navigationSort = 1;

    public static function getModel(): string
    {
        return Utils::getNavigationTypeModel();
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
            NavigationManageWidgets::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->scopeable(static::getScopeType(), static::getScopeId())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
