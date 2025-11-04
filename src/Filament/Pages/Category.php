<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Category\Filament\Pages\Category\Base as BaseCategoryPage;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

class Category extends BaseCategoryPage
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    public static function getScopeType(): string
    {
        return PostResource::getCustomProperty('scopeType') ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return PostResource::getCustomProperty('scopeId') ?? parent::getScopeId();
    }

    public function getLevel(): ?int
    {
        return self::getCustomProperty('level') ?? parent::getLevel();
    }

    public function getEmptyLabel(): ?string
    {
        return self::getCustomProperty('emptyLabel') ?? parent::getEmptyLabel();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
