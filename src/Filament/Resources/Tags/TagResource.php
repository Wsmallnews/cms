<?php

namespace Wsmallnews\Cms\Filament\Resources\Tags;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Tags\Pages\CreateTag;
use Wsmallnews\Cms\Filament\Resources\Tags\Pages\EditTag;
use Wsmallnews\Cms\Filament\Resources\Tags\Pages\ListTags;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;
use Wsmallnews\Support\Filament\Resources\Tags\BaseResource as BaseTagResource;

final class TagResource extends BaseTagResource
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    public static function getModel(): string
    {
        return Utils::getTagModel();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }

    public static function getScopeType(): string
    {
        return Utils::getScopeable()['scope_type'] ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return Utils::getScopeable()['scope_id'] ?? parent::getScopeId();
    }

    public static function getTagType(): string
    {
        return self::getCustomProperty('tag_type') ?? parent::getTagType();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
