<?php

use Illuminate\Support\Facades\Route;
use RalphJSmit\Livewire\Urls\Middleware\LivewireUrlsMiddleware;
use Wsmallnews\Cms\Livewire\Auth\ForgotPassword;
use Wsmallnews\Cms\Livewire\Auth\Login;
use Wsmallnews\Cms\Livewire\Auth\Register;
use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Livewire\Navigation\Navigation;
use Wsmallnews\Cms\Livewire\Post\Post;
use Wsmallnews\Cms\Livewire\Post\Posts;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Http\Middleware\IdentifyTenant;
use Wsmallnews\Support\Support\Utils as SupportUtils;

$middlewares = Utils::getConfig('routes.middleware') ?? [];
SupportUtils::isTenancyEnabled() && array_unshift($middlewares, IdentifyTenant::class);

// 记录路由历史
$middlewares[] = LivewireUrlsMiddleware::class;

Route::domain(Utils::getConfig('routes.domain'))
    ->middleware($middlewares)
    ->prefix(Utils::getConfig('routes.prefix'))
    ->name(Utils::getConfig('routes.name'))
    ->group(function () {
        // 登录
        Route::get(Utils::getConfig('routes.uri.login'), Login::class)->name('login');
        Route::get(Utils::getConfig('routes.uri.register'), Register::class)->name('register');
        Route::get(Utils::getConfig('routes.uri.user-index'), Index::class)->name('user.index');
        Route::get(Utils::getConfig('routes.uri.forgot-password'), ForgotPassword::class)->name('forgot.password');

        Route::get(Utils::getConfig('routes.uri.index'), Index::class)->name('index');
        Route::get(Utils::getConfig('routes.uri.navigation'), Navigation::class)->name('navigation');
        Route::get(Utils::getConfig('routes.uri.posts'), Posts::class)->name('posts');
        Route::get(Utils::getConfig('routes.uri.posts-show'), Post::class)->name('posts.show');
    });

// Route::prefix("tenant/{tenant:slug}")
//     ->name('tenant.')
//     ->middleware(IdentifyTenant::class)
//     // ->domain()
//     ->group(function () {
//         Route::get('/', Index::class)->name('index');
//         Route::get('/navigation/{slug}', Navigation::class)->name('navigation');

//         Route::get('/posts', Posts::class)->name('posts');
//         Route::get('/posts/{id}', Post::class)->name('posts.show');
//         Route::get('/personnels', Personnels::class)->name('personnels');
//         Route::get('/personnels/{id}', Personnel::class)->name('personnels.show');
//     });

// Route::get('test', function () {
//     // $panel = Filament::getCurrentPanel();
//     // $user = auth()->user();
//     // dd($user->getDefaultTenant($panel));

//     // return 'test';
// });
