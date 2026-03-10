<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Category\Filament\Pages\Category\Base as BaseCategoryPage;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

class Category extends BaseCategoryPage
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    protected static ?string $slug = 'post-categories';

    public static function getScopeType(): string
    {
        return self::getCustomScopeType() ?? Utils::getScopeType();
    }

    public static function getScopeId(): int
    {
        return self::getCustomScopeId() ?? Utils::getScopeId();
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
