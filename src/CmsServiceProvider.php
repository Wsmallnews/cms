<?php

namespace Wsmallnews\Cms;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Select;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wsmallnews\Category\Models\Category as CategoryModel;
use Wsmallnews\Cms\Commands\CmsCommand;
use Wsmallnews\Cms\Facades\ContentRegistry as ContentRegistryFacade;
use Wsmallnews\Cms\Filament\Pages\Navigation\Components\BaseNavigation;
use Wsmallnews\Cms\Models\Content as ContentModel;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Cms\Support\Utils;

class CmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'sn-cms';

    public static string $viewNamespace = 'sn-cms';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('wsmallnews/cms');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../routes'))) {
            $package->hasRoutes($this->getRoutes());
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
            $package->runsMigrations();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        // 注册内容类型注册器
        $this->app->singleton(ContentRegistry::class, function (): ContentRegistry {
            return new ContentRegistry;
        });
    }

    public function packageBooted(): void
    {
        // / 注册模型别名
        Relation::enforceMorphMap([
            'sn_content' => ContentModel::class,
            'sn_navigation' => NavigationModel::class,
            'sn_navigation_type' => NavigationTypeModel::class,
            'sn_post' => PostModel::class,
        ]);

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/cms/{$file->getFilename()}"),
                ], 'cms-stubs');
            }
        }

        // 注册组件 (panel 组件)
        Livewire::component('sn-fi-navigation', BaseNavigation::class);

        // 注册组件 (前端组件)
        Livewire::component('sn-components-footer', \Wsmallnews\Cms\Livewire\Components\Footer::class);
        // 导航相关
        Livewire::component('sn-components-navigation', \Wsmallnews\Cms\Livewire\Components\Navigation\Navigation::class);
        Livewire::component('sn-components-navigation-breadcrumb', \Wsmallnews\Cms\Livewire\Components\Navigation\Breadcrumb::class);
        Livewire::component('sn-components-navigation-brothers', \Wsmallnews\Cms\Livewire\Components\Navigation\Brothers::class);
        Livewire::component('sn-components-navigation-container', \Wsmallnews\Cms\Livewire\Components\Navigation\NavigationContainer::class);
        Livewire::component('sn-components-navigation-content', \Wsmallnews\Cms\Livewire\Components\Navigation\Content::class);
        // 内容相关
        Livewire::component('sn-components-index-posts', \Wsmallnews\Cms\Livewire\Components\Post\IndexPosts::class);
        Livewire::component('sn-components-posts', \Wsmallnews\Cms\Livewire\Components\Post\Posts::class);
        Livewire::component('sn-components-post', \Wsmallnews\Cms\Livewire\Components\Post\Post::class);

        // 注册导航内容
        ContentRegistryFacade::registers([
            [
                'type' => 'posts',
                'label' => '图文列表',
                'forms' => fn ($fields) => [
                    // 多选分类
                    SelectTree::make('category_ids')->label('选择分类')
                        ->query(query: function () {
                            return CategoryModel::scopeable(Utils::getScopeType(), Utils::getScopeId());
                        }, titleAttribute: 'name', parentAttribute: 'parent_id')
                        ->searchable()
                        ->multiple()
                        ->enableBranchNode()
                        ->withCount()
                        ->placeholder(__('请选择图文分类'))
                        ->emptyLabel(__('未搜索到分类'))
                        ->treeKey('postCategories'),
                ],
                'components' => [
                    \Wsmallnews\Cms\Livewire\Components\Post\Posts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
            [
                'type' => 'post-detail',
                'label' => '图文详情',
                'forms' => fn ($fields) => [
                    Select::make('id')->label('选择图文')
                        ->options(PostModel::normal()->scopeable(Utils::getScopeType(), Utils::getScopeId())->limit(30)->pluck('title', 'id'))
                        ->getSearchResultsUsing(fn (string $search): array => PostModel::normal()->scopeable(Utils::getScopeType(), Utils::getScopeId())->where('title', 'like', "%{$search}%")->limit(30)->pluck('title', 'id')->toArray())
                        ->placeholder('请选择图文详情')
                        ->searchable()
                        ->preload()
                        ->required(),
                ],
                'components' => [
                    \Wsmallnews\Cms\Livewire\Components\Post\Post::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
        ]);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'wsmallnews/cms';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('cms', __DIR__ . '/../resources/dist/components/cms.js'),
            // Css::make('cms-styles', __DIR__ . '/../resources/dist/cms.css'),
            // Js::make('cms-scripts', __DIR__ . '/../resources/dist/cms.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            CmsCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return ['web'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            '2025_11_01_183836_create_sn_navigation_types_table',
            '2025_11_01_211931_create_sn_navigations_table',
            '2025_11_01_213119_create_sn_contents_table',
            '2025_11_04_111453_create_sn_posts_table',
        ];
    }
}
