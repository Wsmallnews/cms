<?php

namespace Wsmallnews\Cms;

use BezhanSalleh\PluginEssentials\Concerns\Plugin as Essentials;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Cms\Filament\Pages\ManageNavigation as ManageNavigationPage;
use Wsmallnews\Cms\Filament\Pages\Navigation as NavigationPage;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;
use Wsmallnews\Support\Concerns\Plugin\HasCustomProperties;

class CmsPlugin implements Plugin
{
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    use Essentials\HasPluginDefaults;
    use Essentials\WithMultipleResourceSupport;
    use EvaluatesClosures;
    use HasCustomProperties;

    public function getId(): string
    {
        return 'sn-cms';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            NavigationTypeResource::class,
        ])->pages([
            NavigationPage::class,
            ManageNavigationPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    /**
     * 资源默认值
     */
    protected function getPluginDefaults(): array
    {
        return [
            'resources' => [
                NavigationTypeResource::class => [
                    'modelLabel' => '分类类型',
                    'pluralModelLabel' => '分类类型2',

                    'navigationGroup' => '分类管理',
                    'navigationLabel' => '分类类型',
                    'navigationIcon' => Heroicon::Bars3,
                    'activeNavigationIcon' => Heroicon::Bars3,
                    'navigationSort' => 1,
                    'navigationBadge' => null,
                    'navigationBadgeColor' => null,
                    'navigationParentItem' => null,
                    'registerNavigation' => true,

                    'globalSearchResultsLimit' => 50,
                ],
            ],
        ];
    }
}
