<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Wsmallnews\Cms\Filament\Resources\Posts\Schemas\PostForm;
use Wsmallnews\Cms\Filament\Resources\Posts\Tables\PostsTable;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Resources\Concerns\Scopeable;

abstract class BaseResource extends Resource
{
    use Scopeable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocument;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Document;

    protected static ?string $navigationLabel = '图文管理';

    protected static string | UnitEnum | null $navigationGroup = '内容管理';

    protected static ?string $slug = 'posts';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = '图文';

    protected static ?string $pluralModelLabel = '图文';

    protected static ?int $navigationSort = 2;

    public static function getModel(): string
    {
        return Utils::getPostModel();
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyScopeableToQuery(parent::getEloquentQuery())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
