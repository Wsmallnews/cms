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
        $this->categoryId = request()->input('categoryId', 0);
    }

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.home'), 'url' => Utils::route('index')],
            ['label' => __('sn-cms::cms.frontend.posts_list'), 'url' => Utils::route('posts')],
        ];

        return view($this->getThemeView('post.posts'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
