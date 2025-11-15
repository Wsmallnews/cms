<?php

use Wsmallnews\Cms\Enums;
use Wsmallnews\Cms\Models;

return [
    'scopeable' => [
        'scope_type' => 'sn-cms',
        'scope_id' => 0,
    ],

    'models' => [
        'content' => Models\Content::class,
        'navigation' => Models\Navigation::class,
        'navigation_type' => Models\NavigationType::class,
        'post' => Models\Post::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    |
    | Firstly, the panel where the current plugin is located should support multi tenancy before you can set this option. 
    | Secondly, The tenant model should be set as a panel model.
    |
    */
    'tenant_model' => null,

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
         * If you differentiate tenants by url, you should set it like this: cms/{tenant: slug}
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
            'posts_show' => 'posts/{id}',
        ]
    ],

    // 'enums' => [
    //     'navigation_status' => Enums\NavigationStatus::class,
    //     'navigation_type_status' => Enums\NavigationTypeStatus::class,
    //     'post_status' => Enums\PostStatus::class,
    // ],
];
