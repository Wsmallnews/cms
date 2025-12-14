<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Livewire\Attributes\Url;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Posts extends Base
{
    #[Url]
    public int $categoryId;

    public function mount()
    {
        $this->categoryId = request()->get('category_id', 0);
    }

    public function render()
    {
        $breadcrumbs = [
            ['label' => '首页', 'url' => Utils::route('index')],
            ['label' => '资讯列表', 'url' => Utils::route('posts')],
        ];

        return view($this->getThemeView('post.posts'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
