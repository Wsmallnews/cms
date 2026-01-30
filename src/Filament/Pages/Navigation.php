<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BezhanSalleh\PluginEssentials\Concerns;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Pages\Navigation\Base as BaseNavigationPage;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

final class Navigation extends BaseNavigationPage
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    protected function schema(array $arguments): array
    {
        return self::getCustomFormArray($arguments) ?: parent::schema($arguments);
    }

    public function infolistSchema(): array
    {
        return self::getCustomInfolistArray() ?: parent::infolistSchema();
    }

    public static function getScopeType(): string
    {
        return Utils::getScopeable()['scope_type'] ?? parent::getScopeType();
    }

    public static function getScopeId(): int
    {
        return Utils::getScopeable()['scope_id'] ?? parent::getScopeId();
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
