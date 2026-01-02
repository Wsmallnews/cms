<?php

use Illuminate\Support\Facades\Route;
use RalphJSmit\Livewire\Urls\Middleware\LivewireUrlsMiddleware;
use Wsmallnews\Cms\Livewire\Auth\ForgotPassword;
use Wsmallnews\Cms\Livewire\Auth\Login;
use Wsmallnews\Cms\Livewire\Auth\Register;
use Wsmallnews\Cms\Livewire\Auth\ResetPassword;
use Wsmallnews\Cms\Livewire\Auth\VerifyEmail;
use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Livewire\Profile;
use Wsmallnews\Cms\Livewire\Navigation\Navigation;
use Wsmallnews\Cms\Livewire\Post\Post;
use Wsmallnews\Cms\Livewire\Post\Posts;
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
        // 无需登录
        Route::middleware('guest')->group(function () {
            Route::get(Utils::getConfig('routes.uri.login'), Login::class)->name('login');
            Route::get(Utils::getConfig('routes.uri.register'), Register::class)->name('register');
            Route::get(Utils::getConfig('routes.uri.forgot-password'), ForgotPassword::class)->name('forgot.password');
            Route::get(Utils::getConfig('routes.uri.reset-password'), ResetPassword::class)->name('reset.password');

            Route::get(Utils::getConfig('routes.uri.index'), Index::class)->name('index');
            Route::get(Utils::getConfig('routes.uri.navigation'), Navigation::class)->name('navigation');
            Route::get(Utils::getConfig('routes.uri.posts'), Posts::class)->name('posts');
            Route::get(Utils::getConfig('routes.uri.posts-show'), Post::class)->name('posts.show');
        });

        Route::middleware('auth:'. Utils::getConfig('guard'))->group(function () {
            // 验证邮箱
            Route::get(Utils::getConfig('routes.uri.verify-email'), VerifyEmail::class)->name('verify.email');
            Route::get(Utils::getConfig('routes.uri.verify-email-verification'), VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verify.email.verification');

            // 个人中心
            Route::get(Utils::getConfig('routes.uri.profile'), Profile::class)->name('profile');
        });

    });