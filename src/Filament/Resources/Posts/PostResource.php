<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts;

use BezhanSalleh\PluginEssentials\Concerns;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\CreatePost;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\EditPost;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\ListPosts;
use Wsmallnews\Cms\Filament\Resources\Tags\TagResource;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

final class PostResource extends BaseResource
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
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
