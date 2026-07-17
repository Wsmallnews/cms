<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts;

use Closure;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\CreatePost;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\EditPost;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\ListPosts;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\ViewPost;
use Wsmallnews\Cms\Filament\Resources\Tags\TagResource;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Resources\ResourceConfiguration;

final class PostResource extends BaseResource
{
    use CanBeConfigured;

    protected static ?string $configurationClass = ResourceConfiguration::class;

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view' => ViewPost::route('/{record}'),
            'edit' => EditPost::route('/{record}/edit'),
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

    /**
     * Post 是最终 resource，所以 post 就是要用 cms 中的tagResource ，所以这里直接写死 TagResource
     */
    public static function getTagType(): string
    {
        return TagResource::getTagType();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
