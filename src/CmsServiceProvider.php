<?php

namespace Wsmallnews\Cms;

use Filament\Facades\Filament;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\Filesystem;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wsmallnews\Cms\Commands\CmsInstallCommand;
use Wsmallnews\Cms\Http\Middleware\Authenticate;
use Wsmallnews\Cms\Http\Middleware\EnsureEmailIsVerified;
use Wsmallnews\Cms\Http\Middleware\RedirectIfAuthenticated;
use Wsmallnews\Cms\Http\Middleware\RequirePassword;
use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Features\Modules\Module;
use Wsmallnews\Support\Features\Modules\ModuleRegistry;
use Wsmallnews\Support\Features\Site\SiteRegistry;

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
    }

    public function packageRegistered(): void
    {
        // 模块身份登记（ModuleRegistry 单一事实源：类反查/存在性校验/插件实例）
        ModuleRegistry::register(new Module(
            id: static::$name,
            namespace: 'Wsmallnews\\Cms',
            plugin: CmsPlugin::class,
        ));
    }

    public function packageBooted(): void
    {
        // 注册模型别名
        Relation::enforceMorphMap([
            'sn_navigation' => Utils::getNavigationModel(),
            'sn_navigation_type' => Utils::getNavigationTypeModel(),
            'sn_post' => Utils::getPostModel(),
            'sn_link' => Utils::getLinkModel(),
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

        // 模块级注册（配置、内容源、菜单）：与站点无关，只执行一次
        $this->app->make(CmsSiteServices::class)->registerModules();

        // 站点级注册：所有 panel 注册完成后 SiteRegistry 才完整。getPanels() 是冗余预防：
        // 若 Filament 改为懒解析且这里不触发，插件 register() 不执行，本回调取到的站点必为空，站点服务全部缺失。
        $this->app->booted(function (): void {
            Filament::getPanels();

            $pipeline = $this->app->make(CmsSiteServices::class)->pipeline();

            $pipeline->registerAll(app(SiteRegistry::class)->forPlugin(app(CmsPlugin::class)->getId()));
            $pipeline->finish();
        });
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
            'create_sn_links_table',
            'create_sn_compositions_table',
        ];
    }
}
