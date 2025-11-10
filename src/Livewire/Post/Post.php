<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\Livewire\Base;

class Post extends Base
{
    public int $id;

    public function render()
    {
        $breadcrumbs = [
            ['label' => '首页', 'url' => sn_route('cms.index')],
            ['label' => '资讯详情', 'url' => sn_route('cms.posts.show', $this->id)],
        ];

        return view('sn-cms::livewire.post.post', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
