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
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Facades\ContentRegistry as ContentRegistryFacade;
use Wsmallnews\Cms\Facades\FlagRegistry as FlagRegistryFacade;
use Wsmallnews\Cms\Http\Middleware\Authenticate;
use Wsmallnews\Cms\Http\Middleware\EnsureEmailIsVerified;
use Wsmallnews\Cms\Http\Middleware\RedirectIfAuthenticated;
use Wsmallnews\Cms\Http\Middleware\RequirePassword;
use Wsmallnews\Cms\Livewire\Components\Post\IndexPosts;
use Wsmallnews\Cms\Livewire\Components\Post\Post;
use Wsmallnews\Cms\Livewire\Components\Post\Posts;
use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\ScheduledTask;
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

        // Publish settings migrations
        $this->publishes([
            __DIR__ . '/../database/settings/create_sn_general_settings.php.stub' => database_path('settings/create_sn_general_settings.php'),
        ], 'sn-cms-settings');

        // 注册 livewire 命名空间（自动发现 src/Livewire/ 下的组件）
        Livewire::addNamespace(
            namespace: 'sn-cms',
            classNamespace: 'Wsmallnews\\Cms\\Livewire'
        );

        // 路由处理器反向查找注册（Route::get(Index::class) 需要类名→别名映射）
        Livewire::component('sn-cms::index', Index::class);

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
                    'reset-password' => fn ($params) => Utils::route('reset.password', $params),
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
                'label' => __('sn-cms::cms.content_type.posts'),
                'forms' => fn ($fields) => [
                    // 多选分类
                    Group::make()
                        ->schema([
                            ToggleButtons::make('categoryStyle')->label(__('sn-cms::cms.post_form.category_style'))
                                ->default('select')
                                ->options([
                                    'select' => __('sn-cms::cms.post_form.category_style_select'),
                                    'tree' => __('sn-cms::cms.post_form.category_style_tree'),
                                ])
                                ->colors([
                                    'select' => 'warning',
                                    'tree' => 'info',
                                ])
                                ->inline()
                                ->helperText(__('sn-cms::cms.post_form.category_style_helper')),
                            SelectTree::make('categoryIds')->label(__('sn-cms::cms.post_form.categories'))
                                ->query(query: function () {
                                    return CategoryModel::scopeable(Utils::getScopeType(), Utils::getScopeId());
                                }, titleAttribute: 'name', parentAttribute: 'parent_id')
                                ->searchable()
                                ->multiple()
                                ->enableBranchNode()
                                ->withCount()
                                ->placeholder(__('sn-cms::cms.post_form.categories_placeholder'))
                                ->emptyLabel(__('sn-cms::cms.post_form.categories_empty'))
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
                'label' => __('sn-cms::cms.content_type.index_posts'),
                'forms' => fn ($fields) => [],
                'components' => [
                    IndexPosts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
            [
                'type' => 'post-detail',
                'label' => __('sn-cms::cms.content_type.post_detail'),
                'forms' => fn ($fields) => [
                    Group::make()
                        ->schema([
                            Select::make('id')->label(__('sn-cms::cms.post_form.select_post'))
                                ->options(PostModel::published()->scopeable(Utils::getScopeType(), Utils::getScopeId())->limit(30)->pluck('title', 'id'))
                                ->getSearchResultsUsing(fn (string $search): array => PostModel::published()->scopeable(Utils::getScopeType(), Utils::getScopeId())->where('title', 'like', "%{$search}%")->limit(30)->pluck('title', 'id')->toArray())
                                ->placeholder(__('sn-cms::cms.post_form.select_post_placeholder'))
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

        // 注册 Post 的定时调度动作（publish / unpublish）
        ScheduledTask::registers('sn_post', [
            [
                'action' => 'publish',
                'label' => __('sn-cms::cms.post_form.schedule_publish'),
                'forms' => fn () => [],
                'handler' => fn ($task, ?array $payload): bool => $task->schedulable->update([
                    'status' => PostStatus::Published,
                    'published_at' => $task->schedulable->published_at ?? now(),
                ]),
            ],
            [
                'action' => 'unpublish',
                'label' => __('sn-cms::cms.post_form.schedule_unpublish'),
                'forms' => fn () => [],
                'handler' => fn ($task, ?array $payload): bool => $task->schedulable->update([
                    'status' => PostStatus::Hidden,
                ]),
            ],
        ]);

        // 注册用户侧边栏菜单
        SidebarMenuRegistryFacade::registers(app(CmsPlugin::class)->getId(), [
            fn () => [
                'key' => 'profile',
                'label' => __('sn-cms::cms.sidebar.profile'),
                'url' => Utils::route('profile'),
                'icon' => Heroicon::OutlinedUser,
                'active_icon' => Heroicon::User,
            ],
            fn () => [
                'key' => 'profile-views',
                'label' => __('sn-cms::cms.sidebar.profile_views'),
                'url' => Utils::route('profile.views'),
                'icon' => Heroicon::OutlinedEye,
                'active_icon' => Heroicon::Eye,
            ],
            fn () => [
                'key' => 'settings-profile',
                'label' => __('sn-cms::cms.sidebar.settings_profile'),
                'url' => Utils::route('settings.profile'),
                'icon' => Heroicon::OutlinedPencilSquare,
                'active_icon' => Heroicon::PencilSquare,
            ],
            fn () => [
                'key' => 'settings-password',
                'label' => __('sn-cms::cms.sidebar.settings_password'),
                'url' => Utils::route('settings.password'),
                'icon' => Heroicon::OutlinedLockClosed,
                'active_icon' => Heroicon::LockClosed,
            ],
            fn () => [
                'key' => 'settings-two-factor',
                'label' => __('sn-cms::cms.sidebar.settings_two_factor'),
                'url' => fn () => Utils::route('settings.two-factor'),
                'icon' => Heroicon::OutlinedKey,
                'active_icon' => Heroicon::Key,
                'hidden' => fn () => ! Utils::getConfig('two_factor.enabled', false),
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
