<?php

use Illuminate\Support\Facades\Route;
use RalphJSmit\Livewire\Urls\Middleware\LivewireUrlsMiddleware;
use Wsmallnews\Cms\Livewire\Auth\ConfirmPassword;
use Wsmallnews\Cms\Livewire\Auth\ForgotPassword;
use Wsmallnews\Cms\Livewire\Auth\Login;
use Wsmallnews\Cms\Livewire\Auth\Register;
use Wsmallnews\Cms\Livewire\Auth\ResetPassword;
use Wsmallnews\Cms\Livewire\Auth\VerifyEmail;
use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Livewire\Navigation\Navigation;
use Wsmallnews\Cms\Livewire\Post\Post;
use Wsmallnews\Cms\Livewire\Post\Posts;
use Wsmallnews\Cms\Livewire\Profile;
use Wsmallnews\Cms\Livewire\Settings\Password as SettingsPassword;
use Wsmallnews\Cms\Livewire\Settings\Profile as SettingsProfile;
use Wsmallnews\Cms\Livewire\Settings\TwoFactor;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Http\Middleware\IdentifyTenant;
use Wsmallnews\Support\Support\Utils as SupportUtils;
use Wsmallnews\User\Http\Controllers\Auth\VerifyEmailController;

$middlewares = Utils::getConfig('routes.middleware') ?? [];
SupportUtils::isTenancyEnabled() && array_unshift($middlewares, IdentifyTenant::class);

// 记录路由历史
$middlewares[] = LivewireUrlsMiddleware::class;

Route::domain(Utils::getConfig('routes.domain'))
    ->middleware($middlewares)
    ->prefix(Utils::getConfig('routes.prefix'))
    ->name(Utils::getConfig('routes.name'))
    ->group(function () {
        // 不登录api
        Route::middleware('cms-guest:' . Utils::getConfig('guard'))->group(function () {
            Route::get(Utils::getConfig('routes.uri.login'), Login::class)->name('login');
            Route::get(Utils::getConfig('routes.uri.register'), Register::class)->name('register');
            Route::get(Utils::getConfig('routes.uri.forgot-password'), ForgotPassword::class)->name('forgot.password');
            Route::get(Utils::getConfig('routes.uri.reset-password'), ResetPassword::class)->name('reset.password');
        });

        Route::middleware('cms-auth:' . Utils::getConfig('guard'))->group(function () {
            // 验证邮箱
            Route::get(Utils::getConfig('routes.uri.verify-email'), VerifyEmail::class)->name('verify.email');
            Route::get(Utils::getConfig('routes.uri.verify-email-verification'), VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verify.email.verification');

            // 确认密码页，需要验证的页面，添加如下中间件：->middleware(['cms-password.confirm'])
            Route::get(Utils::getConfig('routes.uri.password-confirm'), ConfirmPassword::class)->name('password.confirm');

            // 个人中心
            Route::get(Utils::getConfig('routes.uri.profile'), Profile::class)->name('profile');

            // 个人设置
            Route::get(Utils::getConfig('routes.uri.settings.profile'), SettingsProfile::class)->name('settings.profile');
            Route::get(Utils::getConfig('routes.uri.settings.password'), SettingsPassword::class)->name('settings.password');

            if (Utils::getConfig('two_factor.enabled', false)) {
                // 双因素身份验证
                Route::middleware(['cms-email.verified', 'cms-password.confirm'])->group(function () {
                    Route::get(Utils::getConfig('routes.uri.settings.two-factor'), TwoFactor::class)->name('settings.two-factor');
                });
            }
        });

        // 普通用户路由
        Route::get(Utils::getConfig('routes.uri.index'), Index::class)->name('index');
        Route::get(Utils::getConfig('routes.uri.navigation-show'), Navigation::class)->name('navigation.show');
        Route::get(Utils::getConfig('routes.uri.posts'), Posts::class)->name('posts');
        Route::get(Utils::getConfig('routes.uri.posts-show'), Post::class)->name('posts.show');
    });
