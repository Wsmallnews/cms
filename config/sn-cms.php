<?php

use Wsmallnews\Cms\Enums;
use Wsmallnews\Cms\Models;

return [
    /**
     * Default scopeable
     */
    'scopeable' => [
        'scope_type' => 'sn-cms',
        'scope_id' => 0,
    ],

    /**
     * Custom models
     */
    'models' => [
        'content' => Models\Content::class,
        'navigation' => Models\Navigation::class,
        'navigation_type' => Models\NavigationType::class,
        'post' => Models\Post::class,
    ],

    /**
     * auth guard
     */
    'guard' => 'web',

    /**
     * 文件基础目录，会自动拼接当前年月日 (仅用于 filament 默认上传组件 (Forms\Components\FileUpload))
     */
    'file_directory' => 'sn/cms/',

    'routes' => [
        /**
         * Whether to enable the cms routes.
         */
        'enabled' => true,
        /**
         * The domain where the cms routes should be registered.
         * If you differentiate tenants by domain, you should set it like this: {tenant:slug}.example.com
         */
        'domain' => null,
        /**
         * the middleware you want to apply on all the cms routes
         * for example if you want to make your cms for users only, add the middleware 'auth'.
         */
        'middleware' => ['web'],
        /**
         * Default path for the blog homepage.
         * If you differentiate tenants by url, you should set it like this: cms/{tenant:slug}
         */
        'prefix' => 'cms',
        /**
         * Default name prefix for the cms routes.
         */
        'name' => 'sn-cms.',
        /**
         * default uri for the cms routes
         */
        'uri' => [
            'index' => '/',
            'navigation' => 'navigation/{slug}',
            'posts' => 'posts',
            'posts-show' => 'posts/{id}',

            'login' => 'login',
            'register' => 'register',
            'user-index' => 'user',
            'forgot-password' => 'forgot-password',
        ],
    ],

    'themes' => [
        'layout' => 'sn-cms::components.layouts.app',

        'theme' => 'tradition',

        'containers' => [
            'block-container' => 'sn-cms::base.block-container',
            'item-container' => 'sn-cms::base.item-container',
        ],
    ],

    // 'enums' => [
    //     'navigation_status' => Enums\NavigationStatus::class,
    //     'navigation_type_status' => Enums\NavigationTypeStatus::class,
    //     'post_status' => Enums\PostStatus::class,
    // ],
];
