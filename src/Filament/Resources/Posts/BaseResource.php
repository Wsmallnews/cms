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
use Wsmallnews\Cms\Models\Post;
use Wsmallnews\Support\Filament\Resources\Concerns\Scopeable;

abstract class BaseResource extends Resource
{
    use Scopeable;

    protected static ?string $model = Post::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3;

    protected static ?string $navigationLabel = '图文管理';

    protected static string | UnitEnum | null $navigationGroup = '内容管理';

    protected static ?string $slug = 'posts';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = '图文';

    protected static ?string $pluralModelLabel = '图文';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
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
