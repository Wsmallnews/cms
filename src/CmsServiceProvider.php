<?php

namespace Wsmallnews\Cms;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wsmallnews\Category\Models\Category as CategoryModel;
use Wsmallnews\Cms\Commands\CmsCommand;
use Wsmallnews\Cms\Facades\ContentRegistry as ContentRegistryFacade;
use Wsmallnews\Cms\Filament\Pages\Navigation\Components\BaseNavigation;
use Wsmallnews\Cms\Http\Middleware\Authenticate;
use Wsmallnews\Cms\Http\Middleware\RedirectIfAuthenticated;
use Wsmallnews\Cms\Http\Middleware\RequirePassword;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\User\Facades\AuthsConfig as UserAuthsConfig;

class CmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'sn-cms';

    public static string $viewNamespace = 'sn-cms';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('wsmallnews/cms');
            });

        if (Utils::getConfig('routes.enabled') !== false) {     // 只要不等于 false 就注册路由
            $package->hasRoutes($this->getRoutes());
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
            $package->runsMigrations();
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
            'sn_content' => Utils::getContentModel(),
            'sn_navigation' => Utils::getNavigationModel(),
            'sn_navigation_type' => Utils::getNavigationTypeModel(),
            'sn_post' => Utils::getPostModel(),
        ]);

        // 定义中间件别名
        $this->app['router']->aliasMiddleware('cms-auth', Authenticate::class);
        $this->app['router']->aliasMiddleware('cms-guest', RedirectIfAuthenticated::class);
        $this->app['router']->aliasMiddleware('cms-password.confirm', RequirePassword::class);

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
        Livewire::component('sn-cms-fi-navigation', BaseNavigation::class);

        // 注册组件 (前端组件)
        Livewire::component('sn-cms-components-footer', \Wsmallnews\Cms\Livewire\Components\Footer::class);
        // 导航相关
        Livewire::component('sn-cms-components-navigation', \Wsmallnews\Cms\Livewire\Components\Navigation\Navigation::class);
        Livewire::component('sn-cms-components-navigation-breadcrumb', \Wsmallnews\Cms\Livewire\Components\Navigation\Breadcrumb::class);
        Livewire::component('sn-cms-components-navigation-brothers', \Wsmallnews\Cms\Livewire\Components\Navigation\Brothers::class);
        Livewire::component('sn-cms-components-navigation-container', \Wsmallnews\Cms\Livewire\Components\Navigation\NavigationContainer::class);
        Livewire::component('sn-cms-components-navigation-content', \Wsmallnews\Cms\Livewire\Components\Navigation\Content::class);
        // 内容相关
        Livewire::component('sn-cms-components-index-posts', \Wsmallnews\Cms\Livewire\Components\Post\IndexPosts::class);
        Livewire::component('sn-cms-components-posts', \Wsmallnews\Cms\Livewire\Components\Post\Posts::class);
        Livewire::component('sn-cms-components-post', \Wsmallnews\Cms\Livewire\Components\Post\Post::class);

        // 注册用户认证信息
        UserAuthsConfig::config(app(\Wsmallnews\Cms\CmsPlugin::class)->getId(), function () {
            return [
                'guard' => Utils::getConfig('guard', 'web'),
                'two-factor' => Utils::getConfig('two-factor', []),
                'urls' => [
                    'index' => Utils::route('index'),
                    'login' => Utils::route('login'),
                    'register' => Utils::route('register'),
                    'profile' => Utils::route('profile'),
                    'forgot-password' => Utils::route('forgot.password'),
                    'reset-password' => fn ($params) => Utils::route('reset.password', $params),
                    'verify-email' => Utils::route('verify.email'),
                    'verify-email-verification' => function ($parameters) {
                        // @sn todo ，这里先直接填入 租户参数
                        if (! isset($parameters['tenant'])) {        // 没有租户参数,则添加租户参数
                            $tenant = current_tenant();
                            $parameters['tenant'] = $tenant;        // 租户参数
                        }

                        $parameters['module'] = app(\Wsmallnews\Cms\CmsPlugin::class)->getId();             // 当前模块名

                        return URL::temporarySignedRoute(
                            Utils::getConfig('routes.name', '') . 'verify.email.verification',
                            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                            $parameters
                        );
                    },
                    'password-confirm' => Utils::route('password.confirm'),
                ],
            ];
        });

        // 注册导航内容
        ContentRegistryFacade::registers([
            [
                'type' => 'posts',
                'label' => '图文列表',
                'forms' => fn ($fields) => [
                    // 多选分类
                    Group::make()
                        ->schema([
                            SelectTree::make('categoryIds')->label('选择分类')
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
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Toggle::make('needCustomStyle')
                        ->label('自定义组件样式')
                        ->default(false)
                        ->helperText('开启自定义组件样式, 可以自定义视图，容器包装起等')
                        ->inline(false),
                    Group::make()
                        ->schema([
                            Fieldset::make('view')
                                ->label('自定义视图')
                                ->schema([
                                    Toggle::make('hasCustomView')
                                        ->label('自定义视图')
                                        ->default(false)
                                        ->helperText('开启自定义视图, 将会使用自定义视图')
                                        ->inline(false),
                                    TextInput::make('view')->label('自定义视图')
                                        ->placeholder('请输入自定义视图地址')
                                        ->required(fn (Get $get) => (bool) $get('hasCustomView'))
                                        ->markAsRequired()
                                        ->visibleJs(<<<'JS'
                                            $get('hasCustomView')
                                        JS),
                                ])->columns(1),
                            Fieldset::make('blockContainer')
                                ->label('块容器包装器')
                                ->schema([
                                    Toggle::make('hasDefaultBlockContainerWrapper')
                                        ->label('容器包装器')
                                        ->default(false)
                                        ->helperText('开启容器包装器, 将会在组件外包一层包装器')
                                        ->inline(false),
                                    TextInput::make('blockContainerWrapperView')->label('自定义包装器')
                                        ->placeholder('不填写将使用默认包装器')
                                        ->helperText('如果不填写将使用默认包装器')
                                        ->visibleJs(<<<'JS'
                                            $get('hasDefaultBlockContainerWrapper')
                                        JS),
                                ])->columns(1),
                            Fieldset::make('itemContainer')
                                ->label('项容器包装器')
                                ->schema([
                                    Toggle::make('hasDefaultItemContainerWrapper')
                                        ->label('容器包装器')
                                        ->default(true)
                                        ->helperText('开启容器包装器, 将会在组件列表项外包一层包装器')
                                        ->inline(false),
                                    TextInput::make('itemContainerWrapperView')->label('自定义包装器')
                                        ->placeholder('不填写将使用默认包装器')
                                        ->helperText('如果不填写将使用默认包装器')
                                        ->visibleJs(<<<'JS'
                                            $get('hasDefaultItemContainerWrapper')
                                        JS),
                                ])->columns(1),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->visibleJs(<<<'JS'
                            $get('needCustomStyle')
                        JS),
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
                    Group::make()
                        ->schema([
                            Select::make('id')->label('选择图文')
                                ->options(PostModel::normal()->scopeable(Utils::getScopeType(), Utils::getScopeId())->limit(30)->pluck('title', 'id'))
                                ->getSearchResultsUsing(fn (string $search): array => PostModel::normal()->scopeable(Utils::getScopeType(), Utils::getScopeId())->where('title', 'like', "%{$search}%")->limit(30)->pluck('title', 'id')->toArray())
                                ->placeholder('请选择图文详情')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Toggle::make('needCustomStyle')
                        ->label('自定义组件样式')
                        ->default(false)
                        ->helperText('开启自定义组件样式, 可以自定义视图，容器包装起等')
                        ->inline(false),

                    Group::make()
                        ->schema([
                            Fieldset::make('view')
                                ->label('自定义视图')
                                ->schema([
                                    Toggle::make('hasCustomView')
                                        ->label('自定义视图')
                                        ->default(false)
                                        ->helperText('开启自定义视图, 将会使用自定义视图')
                                        ->inline(false),
                                    TextInput::make('view')->label('自定义视图')
                                        ->placeholder('请输入自定义视图地址')
                                        ->required(fn (Get $get) => (bool) $get('hasCustomView'))
                                        ->markAsRequired()
                                        ->visibleJs(<<<'JS'
                                            $get('hasCustomView')
                                        JS),
                                ])->columns(1),
                            Fieldset::make('blockContainer')
                                ->label('块容器包装器')
                                ->schema([
                                    Toggle::make('hasDefaultBlockContainerWrapper')
                                        ->label('容器包装器')
                                        ->default(false)
                                        ->helperText('开启容器包装器, 将会在组件外包一层包装器')
                                        ->inline(false),
                                    TextInput::make('blockContainerWrapperView')->label('自定义包装器')
                                        ->placeholder('不填写将使用默认包装器')
                                        ->helperText('如果不填写将使用默认包装器')
                                        ->visibleJs(<<<'JS'
                                            $get('hasDefaultBlockContainerWrapper')
                                        JS),
                                ])->columns(1),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->visibleJs(<<<'JS'
                            $get('needCustomStyle')
                        JS),
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
