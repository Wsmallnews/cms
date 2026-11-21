<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Livewire\Attributes\Url;
use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Support\Facades\Seo;

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
        Seo::title(__('sn-cms::cms.frontend.posts_list'));

        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.home'), 'url' => $this->site()?->route('index')],
            ['label' => __('sn-cms::cms.frontend.posts_list'), 'url' => $this->site()?->route('posts')],
        ];

        return view($this->getThemeView('post.posts'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout($this->getLayout());
    }
}
