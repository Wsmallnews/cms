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
use Wsmallnews\Comment\Filament\Pages\Comment\Widgets\Comment as CommentWidget;
use Wsmallnews\Preference\Filament\Pages\Preference\Widgets\Views as ViewsWidget;
use Wsmallnews\Support\Filament\Resources\Concerns\Scopeable;

abstract class BaseResource extends Resource
{
    use Scopeable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::DocumentText;

    protected static ?string $slug = 'posts';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getModel(): string
    {
        return Utils::getPostModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.post_resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('sn-cms::cms.post_resource.plural_model_label');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? __('sn-cms::cms.post_resource.navigation_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::$navigationGroup ?? __('sn-cms::cms.global_default.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            CommentWidget::class,
            ViewsWidget::class,
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
