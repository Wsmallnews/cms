<?php

namespace Wsmallnews\Cms;

use BezhanSalleh\PluginEssentials\Concerns\Plugin as Essentials;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Cms\Filament\Pages\Category as CategoryPage;
use Wsmallnews\Cms\Filament\Pages\ManageNavigation as ManageNavigationPage;
use Wsmallnews\Cms\Filament\Pages\Navigation as NavigationPage;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
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
            PostResource::class,
        ])->pages([
            NavigationPage::class,
            CategoryPage::class
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
                    // hasLabels
                    'modelLabel' => '导航类型',
                    'pluralModelLabel' => '导航类型',
                    'recordTitleAttribute' => 'name',
                    // 'titleCaseModelLabel' => true,

                    // hasNavigation
                    'navigationLabel' => '导航类型',
                    'navigationIcon' => Heroicon::Bars3BottomLeft,
                    'activeNavigationIcon' => Heroicon::Bars3BottomLeft,
                    'navigationGroup' => '导航管理',
                    'navigationSort' => 1,
                    'navigationBadge' => null,
                    'navigationBadgeColor' => null,
                    'navigationParentItem' => null,
                    'registerNavigation' => true,

                    // hasGlobalSearch
                    'globallySearchable' => false,
                    'globalSearchResultsLimit' => 50,
                    'forceGlobalSearchCaseInsensitive' => null,
                    'splitGlobalSearchTerms' => false,

                    // belongsToParent
                    'parentResource' => null,

                    // BelongsToTenant
                    'scopeToTenant' => true,
                    'tenantRelationshipName' => null,
                    'tenantOwnershipRelationshipName' => null,
                ],
                ManageNavigationPage::class => [
                    // hasLabels
                    'recordTitleAttribute' => 'name',

                    // hasNavigation
                    'navigationLabel' => '导航设置',
                    'navigationIcon' => Heroicon::Bars3,
                    'activeNavigationIcon' => Heroicon::Bars3,
                    'navigationGroup' => '导航管理',
                    'navigationSort' => 1,
                    'navigationBadge' => null,
                    'navigationBadgeColor' => null,
                    'navigationParentItem' => null,
                    'registerNavigation' => true,

                    // hasGlobalSearch
                    'globallySearchable' => false,
                    'globalSearchResultsLimit' => 50,
                    'forceGlobalSearchCaseInsensitive' => null,
                    'splitGlobalSearchTerms' => false,

                    // belongsToParent
                    'parentResource' => null,
                ],
                NavigationPage::class => [
                    // hasLabels
                    'recordTitleAttribute' => 'name',

                    // hasNavigation
                    'navigationLabel' => '导航',
                    'navigationIcon' => Heroicon::Bars3,
                    'activeNavigationIcon' => Heroicon::Bars3,
                    'navigationGroup' => '导航管理',
                    'navigationSort' => 1,
                    'navigationBadge' => null,
                    'navigationBadgeColor' => null,
                    'navigationParentItem' => null,
                    'registerNavigation' => true,

                    // hasGlobalSearch
                    'globallySearchable' => false,
                    'globalSearchResultsLimit' => 50,
                    'forceGlobalSearchCaseInsensitive' => null,
                    'splitGlobalSearchTerms' => false,

                    // belongsToParent
                    'parentResource' => null,
                ],
                PostResource::class => [
                    // hasLabels
                    'modelLabel' => '图文内容',
                    'pluralModelLabel' => '图文内容',
                    'recordTitleAttribute' => 'title',
                    // 'titleCaseModelLabel' => true,

                    // hasNavigation
                    'navigationLabel' => '图文管理',
                    'navigationIcon' => Heroicon::Bars3BottomLeft,
                    'activeNavigationIcon' => Heroicon::Bars3BottomLeft,
                    'navigationGroup' => 'Cms管理',
                    'navigationSort' => 1,
                    'navigationBadge' => null,
                    'navigationBadgeColor' => null,
                    'navigationParentItem' => null,
                    'registerNavigation' => true,

                    // hasGlobalSearch
                    'globallySearchable' => false,
                    'globalSearchResultsLimit' => 50,
                    'forceGlobalSearchCaseInsensitive' => null,
                    'splitGlobalSearchTerms' => false,

                    // belongsToParent
                    'parentResource' => null,

                    // BelongsToTenant
                    'scopeToTenant' => true,
                    'tenantRelationshipName' => null,
                    'tenantOwnershipRelationshipName' => null,

                    // HasCustomProperties
                    'customProperties' => [
                        'scopeType' => 'cms',
                        'scopeId' => 0,
                    ]
                ],
                CategoryPage::class => [
                    // hasLabels
                    'modelLabel' => '分类',
                    'pluralModelLabel' => '图文分类管理',
                    'recordTitleAttribute' => 'name',

                    // hasNavigation
                    'navigationLabel' => '图文分类',
                    'navigationIcon' => Heroicon::Bars3,
                    'activeNavigationIcon' => Heroicon::Bars3,
                    'navigationGroup' => 'Cms管理',
                    'navigationSort' => 1,
                    'navigationBadge' => null,
                    'navigationBadgeColor' => null,
                    'navigationParentItem' => '图文管理',
                    'registerNavigation' => true,

                    // hasGlobalSearch
                    'globallySearchable' => false,
                    'globalSearchResultsLimit' => 50,
                    'forceGlobalSearchCaseInsensitive' => null,
                    'splitGlobalSearchTerms' => false,

                    // belongsToParent
                    'parentResource' => null,

                    // HasCustomProperties
                    'customProperties' => [
                        'level' => 2
                    ]
                ]
            ],
        ];
    }
}
