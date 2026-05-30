<?php

namespace Wsmallnews\Cms;

use BezhanSalleh\PluginEssentials\Concerns\Plugin as Essentials;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Cms\Filament\Pages\Category as CategoryPage;
use Wsmallnews\Cms\Filament\Pages\GeneralSetting as GeneralSettingPage;
use Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Cms\Filament\Resources\Tags\TagResource;
use Wsmallnews\Cms\Support\Utils;
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
        if (Utils::getPanelRegister('pages')) {
            $panel->pages([
                ...Utils::getPanelRegister('pages'),
            ]);
        }

        if (Utils::getPanelRegister('resources')) {
            $panel->resources([
                ...Utils::getPanelRegister('resources'),
            ]);
        }
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
            'navigationGroup' => fn () => __('sn-cms::cms.global_default.navigation_group'),
            'globallySearchable' => false,
            'globalSearchResultsLimit' => 25,

            'resources' => [
                NavigationTypeResource::class => [
                    'modelLabel' => fn () => __('sn-cms::cms.navigation_type_resource.model_label'),
                    'pluralModelLabel' => fn () => __('sn-cms::cms.navigation_type_resource.plural_model_label'),

                    'navigationLabel' => fn () => __('sn-cms::cms.navigation_type_resource.navigation_label'),
                    'navigationIcon' => Heroicon::OutlinedBars3BottomRight,
                    'activeNavigationIcon' => Heroicon::Bars3BottomRight,
                    'navigationSort' => 1,
                ],
                NavigationPage::class => [
                    'navigationLabel' => fn () => __('sn-cms::cms.navigation_page.navigation_label'),
                    'navigationIcon' => Heroicon::OutlinedBars3BottomLeft,
                    'activeNavigationIcon' => Heroicon::Bars3,
                    'navigationSort' => 1,
                ],
                PostResource::class => [
                    'modelLabel' => fn () => __('sn-cms::cms.post_resource.model_label'),
                    'pluralModelLabel' => fn () => __('sn-cms::cms.post_resource.plural_model_label'),

                    'navigationLabel' => fn () => __('sn-cms::cms.post_resource.navigation_label'),
                    'navigationIcon' => Heroicon::OutlinedDocumentText,
                    'activeNavigationIcon' => Heroicon::DocumentText,
                    'navigationSort' => 2,
                ],
                TagResource::class => [
                    'modelLabel' => fn () => __('sn-cms::cms.tag_resource.model_label'),
                    'pluralModelLabel' => fn () => __('sn-cms::cms.tag_resource.plural_model_label'),

                    'navigationLabel' => fn () => __('sn-cms::cms.tag_resource.navigation_label'),
                    'navigationIcon' => Heroicon::OutlinedTag,
                    'activeNavigationIcon' => Heroicon::Tag,
                    'navigationSort' => 3,

                    'customProperties' => [
                        'tag_type' => 'post_tag',
                    ],
                ],
                CategoryPage::class => [
                    'modelLabel' => fn () => __('sn-cms::cms.category_page.model_label'),
                    'pluralModelLabel' => fn () => __('sn-cms::cms.category_page.plural_model_label'),

                    'navigationLabel' => fn () => __('sn-cms::cms.category_page.navigation_label'),
                    'navigationIcon' => Heroicon::OutlinedBars3BottomLeft,
                    'activeNavigationIcon' => Heroicon::Bars3BottomLeft,
                    'navigationSort' => 1,

                    'customProperties' => [
                        'level' => 2,
                    ],
                ],
                GeneralSettingPage::class => [
                    'navigationLabel' => fn () => __('sn-cms::cms.general_setting_page.navigation_label'),
                    'navigationIcon' => Heroicon::OutlinedCog6Tooth,
                    'activeNavigationIcon' => Heroicon::OutlinedCog6Tooth,
                    'navigationSort' => 3,
                ],
            ],
        ];
    }
}
