<?php

namespace Wsmallnews\Cms\Support;

use Illuminate\Support\Facades\Route;
use RalphJSmit\Livewire\Urls\Middleware\LivewireUrlsMiddleware;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Auth\ConfirmPassword;
use Wsmallnews\Cms\Livewire\Auth\ForgotPassword;
use Wsmallnews\Cms\Livewire\Auth\Login;
use Wsmallnews\Cms\Livewire\Auth\Register;
use Wsmallnews\Cms\Livewire\Auth\ResetPassword;
use Wsmallnews\Cms\Livewire\Auth\VerifyEmail;
use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Livewire\Pages\Page as PagesPage;
use Wsmallnews\Cms\Livewire\Post\Post;
use Wsmallnews\Cms\Livewire\Post\Posts;
use Wsmallnews\Cms\Livewire\Profile;
use Wsmallnews\Cms\Livewire\Profile\Views;
use Wsmallnews\Cms\Livewire\Search;
use Wsmallnews\Cms\Livewire\Settings\Password as SettingsPassword;
use Wsmallnews\Cms\Livewire\Settings\Profile as SettingsProfile;
use Wsmallnews\Cms\Livewire\Settings\TwoFactor;
use Wsmallnews\Support\Facades\Feed;
use Wsmallnews\Support\Facades\Sitemap;
use Wsmallnews\Support\Facades\SiteRoutes;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\User\Http\Controllers\Auth\VerifyEmailController;

/**
 * Registers the public cms routes for one site.
 *
 * Prefix, domain, middleware and route-name prefix come from the Site
 * definition; URI paths come from the site's effective `route_uris` options
 * (module defaults merged with site overrides).
 */
class CmsFrontendRoutes
{
    /** Public site routes: no sign-in requirement. */
    public const SLOT_PUBLIC = 'public';

    /** Guest-only routes, registered inside `cms-guest:{guard}`. */
    public const SLOT_GUEST = 'guest';

    /** Authenticated routes, registered inside `cms-auth:{guard}`. */
    public const SLOT_AUTH = 'auth';

    public static function register(Site $site): void
    {
        $pluginId = app(CmsPlugin::class)->getId();
        $guard = (string) $site->getOption('auth.guard', 'web');

        $middlewares = $site->middleware;

        // Site + tenant resolution in one pass (tenant follows the site mode)
        $middlewares[] = 'sn-resolve-site-context';

        // 认证主体可用性校验（guard 由站点 auth.guard 决定，中间件自己读取）
        $middlewares[] = 'sn-ensure-user-active';

        if ((string) $site->getOption('auth.user_type', 'member') === 'member') {
            // 会员状态校验；身份解析由 SiteContext 按 auth.user_type 完成
            $middlewares[] = 'sn-ensure-member-active';
        }

        // 记录路由历史
        $middlewares[] = LivewireUrlsMiddleware::class;

        // 首屏解析页面 SEO 上下文（模块归属由路由声明，中间件在 support 包注册）
        $middlewares[] = 'sn-resolve-seo:' . $pluginId;

        $group = Route::middleware($middlewares)
            ->prefix($site->prefix)
            ->name($site->routeNamePrefix);

        if (filled($site->domain)) {
            $group->domain($site->domain);
        }

        $group->group(function () use ($site, $pluginId, $guard): void {
            // RSS 订阅模块端点（仅输出本模块流；全局 /feed 已退役）。
            // 必须注册在 pages/{slug} 等动态段路由之前，否则会被吃掉。
            if ((bool) $site->getOption('feed.enabled', true)) {
                Feed::routes($pluginId, $site);
            }

            // 站点级 sitemap 端点：归属当前模块，缓存键与 scope 都按当前 site 隔离。
            if ((bool) $site->getOption('sitemap.enabled', true)) {
                Sitemap::routes($pluginId, $site);
            }

            // 不登录api
            Route::middleware('cms-guest:' . $guard)->group(function () use ($site, $pluginId): void {
                Route::get(self::uri($site, 'login'), Login::class)->name('login');
                Route::get(self::uri($site, 'register'), Register::class)->name('register');
                Route::get(self::uri($site, 'forgot-password'), ForgotPassword::class)->name('forgot.password');
                Route::get(self::uri($site, 'reset-password'), ResetPassword::class)->name('reset.password');

                // 宿主扩展：仅未登录可见的路由
                SiteRoutes::apply($pluginId, $site, self::SLOT_GUEST);
            });

            Route::middleware('cms-auth:' . $guard)->group(function () use ($site, $pluginId): void {
                // 验证邮箱
                Route::get(self::uri($site, 'verify-email'), VerifyEmail::class)->name('verify.email');
                Route::get(self::uri($site, 'verify-email-verification'), VerifyEmailController::class)
                    ->middleware(['signed', 'throttle:6,1'])
                    ->name('verify.email.verification');

                // 确认密码页，需要验证的页面，添加如下中间件：->middleware(['cms-password.confirm'])
                Route::get(self::uri($site, 'password-confirm'), ConfirmPassword::class)->name('password.confirm');

                // 个人中心
                Route::get(self::uri($site, 'profile'), Profile::class)->name('profile');
                // 浏览记录
                Route::get(self::uri($site, 'profile-views'), Views::class)->name('profile.views');

                // 个人设置
                Route::get(self::uri($site, 'settings.profile'), SettingsProfile::class)->name('settings.profile');
                Route::get(self::uri($site, 'settings.password'), SettingsPassword::class)->name('settings.password');

                if ((bool) $site->getOption('two_factor.enabled', false)) {
                    // 双因素身份验证
                    Route::middleware(['cms-email.verified', 'cms-password.confirm'])->group(function () use ($site): void {
                        Route::get(self::uri($site, 'settings.two-factor'), TwoFactor::class)->name('settings.two-factor');
                    });
                }

                // 宿主扩展：登录后可访问的路由
                SiteRoutes::apply($pluginId, $site, self::SLOT_AUTH);
            });

            // 普通用户路由
            Route::get(self::uri($site, 'index'), Index::class)->name('index');
            Route::get(self::uri($site, 'posts'), Posts::class)->name('posts');
            Route::get(self::uri($site, 'posts-show'), Post::class)->name('posts.show');

            // 宿主扩展：无需登录的公开路由（须在 pages/{slug} 等动态段之前）
            SiteRoutes::apply($pluginId, $site, self::SLOT_PUBLIC);

            // 站点页面（Page 实体：slug → composition 绑定）
            Route::get(self::uri($site, 'pages-show'), PagesPage::class)->name('pages.show');

            if (self::searchEnabled($site)) {
                // 搜索结果页（页面地址由 site route_uris.search 配置；回车跳转地址经 Search::page 注册）
                Route::get(self::uri($site, 'search'), Search::class)->name('search');
            }
        });

        // Routes are added before their fluent ->name() suffix; refresh the
        // name index so route()/sn_route() can resolve them immediately.
        Route::getRoutes()->refreshNameLookups();
    }

    /** Whether this site exposes the search results page (and its robots rule). */
    public static function searchEnabled(Site $site): bool
    {
        return (bool) $site->getOption('search.enabled', false)
            && $site->getOption('search.display', 'dropdown') === 'page';
    }

    /** Search page disallow path for robots.txt; null when the site has no search page. */
    public static function searchDisallow(Site $site): ?string
    {
        if (! self::searchEnabled($site)) {
            return null;
        }

        $path = trim($site->prefix . '/' . (string) $site->getOption('route_uris.search', 'search'), '/');

        return (string) preg_replace('/\{[^}]+\}/', '*', $path);
    }

    /** Effective URI for a site, e.g. uri($site, 'settings.profile'). */
    public static function uri(Site $site, string $key): string
    {
        return (string) $site->getOption(
            "route_uris.{$key}",
            $key === 'index' ? '/' : str_replace('.', '/', $key)
        );
    }
}
