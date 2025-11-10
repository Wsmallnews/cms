<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Livewire\Attributes\Url;
use Wsmallnews\Cms\Livewire\Base;

class Posts extends Base
{
    #[Url]
    public int $category_id;

    public function mount()
    {
        $this->category_id = request()->get('category_id', 0);
    }

    public function render()
    {
        $breadcrumbs = [
            ['label' => '首页', 'url' => sn_route('cms.index')],
            ['label' => '资讯列表', 'url' => sn_route('cms.posts')],
        ];

        return view('sn-cms::livewire.post.posts', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
