<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\CreatePost;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\EditPost;
use Wsmallnews\Cms\Filament\Resources\Posts\Pages\ListPosts;
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

    public static function getScopeType(): string
    {
        return self::getCustomProperty('scopeType') ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return self::getCustomProperty('scopeId') ?? parent::getScopeId();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
