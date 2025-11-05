<?php

use Wsmallnews\Cms\Livewire\Index;
use Wsmallnews\Cms\Livewire\Navigation\Navigation;
use Wsmallnews\Cms\Livewire\Post\Posts;
use Wsmallnews\Cms\Livewire\Post\Post;
// use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;

Route::prefix("cms")
    ->name('cms.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');
        Route::get('/navigation/{slug}', Navigation::class)->name('navigation');
        Route::get('/posts', Posts::class)->name('posts');
        Route::get('/posts/{id}', Post::class)->name('posts.show');
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
