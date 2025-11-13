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

    // 'enums' => [
    //     'navigation_status' => Enums\NavigationStatus::class,
    //     'navigation_type_status' => Enums\NavigationTypeStatus::class,
    //     'post_status' => Enums\PostStatus::class,
    // ],
];
