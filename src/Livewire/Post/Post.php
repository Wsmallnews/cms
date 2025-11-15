<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Post extends Base
{
    public int $id;

    public function render()
    {
        $breadcrumbs = [
            ['label' => '首页', 'url' => Utils::route('index')],
            ['label' => '资讯详情', 'url' => Utils::route('posts.show', $this->id)],
        ];

        return view('sn-cms::livewire.post.post', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
