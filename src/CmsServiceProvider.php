<?php

namespace Wsmallnews\Cms;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wsmallnews\Category\Models\Category as CategoryModel;
use Wsmallnews\Cms\Commands\CmsInstallCommand;
use Wsmallnews\Cms\Facades\ContentRegistry as ContentRegistryFacade;
use Wsmallnews\Cms\Facades\FlagRegistry as FlagRegistryFacade;
use Wsmallnews\Cms\Filament\Pages\Navigation\Components\BaseNavigation;
use Wsmallnews\Cms\Http\Middleware\Authenticate;
use Wsmallnews\Cms\Http\Middleware\EnsureEmailIsVerified;
use Wsmallnews\Cms\Http\Middleware\RedirectIfAuthenticated;
use Wsmallnews\Cms\Http\Middleware\RequirePassword;
use Wsmallnews\Cms\Livewire\Components\Footer;
use Wsmallnews\Cms\Livewire\Components\Navigation\Breadcrumb;
use Wsmallnews\Cms\Livewire\Components\Navigation\Brothers;
use Wsmallnews\Cms\Livewire\Components\Navigation\Content;
use Wsmallnews\Cms\Livewire\Components\Navigation\Navigation;
use Wsmallnews\Cms\Livewire\Components\Navigation\NavigationContainer;
use Wsmallnews\Cms\Livewire\Components\Post\IndexPosts;
use Wsmallnews\Cms\Livewire\Components\Post\Post;
use Wsmallnews\Cms\Livewire\Components\Post\Posts;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\User\Facades\SidebarMenuRegistry as SidebarMenuRegistryFacade;
use Wsmallnews\User\Facades\UserConfig as UserConfigFacade;

class CmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'sn-cms';

    public static string $viewNamespace = 'sn-cms';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasConfigFile()
            ->hasMigrations($this->getMigrations())
            ->hasTranslations()
            ->hasViews(static::$viewNamespace);

        if (Utils::getConfig('routes.enabled') !== false) {     // 只要不等于 false 就注册路由
            $package->hasRoutes($this->getRoutes());
        }
    }

    public function packageRegistered(): void
    {
        // 注册内容类型注册器
        $this->app->singleton(ContentRegistry::class, function (): ContentRegistry {
            return new ContentRegistry;
        });

        // 注册推荐标签注册器
        $this->app->singleton(FlagRegistry::class, function (): FlagRegistry {
            return new FlagRegistry;
        });
    }

    public function packageBooted(): void
    {
        // / 注册模型别名
        Relation::enforceMorphMap([
            'sn_navigation' => Utils::getNavigationModel(),
            'sn_navigation_type' => Utils::getNavigationTypeModel(),
            'sn_post' => Utils::getPostModel(),
        ]);

        // 定义中间件别名
        $this->app['router']->aliasMiddleware('cms-auth', Authenticate::class);
        $this->app['router']->aliasMiddleware('cms-guest', RedirectIfAuthenticated::class);
        $this->app['router']->aliasMiddleware('cms-password.confirm', RequirePassword::class);
        $this->app['router']->aliasMiddleware('cms-email.verified', EnsureEmailIsVerified::class);

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
        Livewire::component('sn-cms-components-footer', Footer::class);
        // 导航相关
        Livewire::component('sn-cms-components-navigation', Navigation::class);
        Livewire::component('sn-cms-components-navigation-breadcrumb', Breadcrumb::class);
        Livewire::component('sn-cms-components-navigation-brothers', Brothers::class);
        Livewire::component('sn-cms-components-navigation-container', NavigationContainer::class);
        Livewire::component('sn-cms-components-navigation-content', Content::class);
        // 内容相关
        Livewire::component('sn-cms-components-index-posts', IndexPosts::class);
        Livewire::component('sn-cms-components-posts', Posts::class);
        Livewire::component('sn-cms-components-post', Post::class);

        // 注册用户认证信息
        UserConfigFacade::config(app(CmsPlugin::class)->getId(), function () {
            return [
                'guard' => Utils::getConfig('guard', 'web'),
                'two_factor' => Utils::getConfig('two_factor', []),
                'urls' => [
                    'index' => Utils::route('index'),
                    'login' => Utils::route('login'),
                    'register' => Utils::route('register'),
                    'profile' => Utils::route('profile'),
                    'forgot-password' => Utils::route('forgot.password'),
                    'reset-password' => fn($params) => Utils::route('reset.password', $params),
                    'verify-email' => Utils::route('verify.email'),
                    'verify-email-verification' => function ($parameters) {
                        // @sn todo ，这里先直接填入 租户参数
                        if (! isset($parameters['tenant'])) {        // 没有租户参数,则添加租户参数
                            $tenant = current_tenant();
                            $parameters['tenant'] = $tenant;        // 租户参数
                        }

                        $parameters['module'] = app(CmsPlugin::class)->getId();             // 当前模块名

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

        // 注册 flag 数组
        FlagRegistryFacade::registers(Utils::getScopeType(), Utils::getFlags());

        // 注册导航内容
        ContentRegistryFacade::registers(Utils::getScopeType(), [
            [
                'type' => 'posts',
                'label' => '图文列表',
                'forms' => fn($fields) => [
                    // 多选分类
                    Group::make()
                        ->schema([
                            ToggleButtons::make('categoryStyle')->label('分类类型样式')
                                ->default('select')
                                ->options([
                                    'select' => __('选择指定分类'),
                                    'tree' => __('显示分类树'),
                                ])
                                ->colors([
                                    'select' => 'warning',
                                    'tree' => 'info',
                                ])
                                ->inline()
                                ->helperText(__('选择全部分类将在页面左侧显示分类树')),
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
                                ->treeKey('postCategories')
                                ->visibleJs(<<<'JS'
                                    $get('categoryStyle') == 'select'
                                JS),
                        ])
                        ->columns(['md' => 2])
                        ->columnSpanFull(),
                ],
                'components' => [
                    Posts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
            [
                'type' => 'index-posts',
                'label' => '图文轮播列表',
                'forms' => fn($fields) => [],
                'components' => [
                    IndexPosts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
            [
                'type' => 'post-detail',
                'label' => '图文详情',
                'forms' => fn($fields) => [
                    Group::make()
                        ->schema([
                            Select::make('id')->label('选择图文')
                                ->options(PostModel::published()->scopeable(Utils::getScopeType(), Utils::getScopeId())->limit(30)->pluck('title', 'id'))
                                ->getSearchResultsUsing(fn(string $search): array => PostModel::published()->scopeable(Utils::getScopeType(), Utils::getScopeId())->where('title', 'like', "%{$search}%")->limit(30)->pluck('title', 'id')->toArray())
                                ->placeholder('请选择图文详情')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->columns(['md' => 2])
                        ->columnSpanFull(),
                ],
                'components' => [
                    Post::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
        ]);

        // 注册用户侧边栏菜单
        SidebarMenuRegistryFacade::registers(app(CmsPlugin::class)->getId(), [
            fn() => [
                'key' => 'profile',
                'label' => '个人中心',
                'url' => Utils::route('profile'),
                'icon' => Heroicon::OutlinedUser,
                'active_icon' => Heroicon::User,
            ],
            fn() => [
                'key' => 'profile-views',
                'label' => '浏览记录',
                'url' => Utils::route('profile.views'),
                'icon' => Heroicon::OutlinedEye,
                'active_icon' => Heroicon::Eye,
            ],
            fn() => [
                'key' => 'settings-profile',
                'label' => '修改资料',
                'url' => Utils::route('settings.profile'),
                'icon' => Heroicon::OutlinedPencilSquare,
                'active_icon' => Heroicon::PencilSquare,
            ],
            fn() => [
                'key' => 'settings-password',
                'label' => '修改密码',
                'url' => Utils::route('settings.password'),
                'icon' => Heroicon::OutlinedLockClosed,
                'active_icon' => Heroicon::LockClosed,
            ],
            fn() => [
                'key' => 'settings-two-factor',
                'label' => '双因素认证',
                'url' => fn() => Utils::route('settings.two-factor'),
                'icon' => Heroicon::OutlinedKey,
                'active_icon' => Heroicon::Key,
                'hidden' => fn() => ! Utils::getConfig('two_factor.enabled', false),
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
            CmsInstallCommand::class,
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
            'create_sn_navigation_types_table',
            'create_sn_navigations_table',
            'create_sn_posts_table',
        ];
    }
}
