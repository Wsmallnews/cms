<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class Post extends Base
{
    public string $slug;

    public function render()
    {
        $breadcrumbs = [
            ['label' => __('sn-cms::cms.frontend.home'), 'url' => Utils::route('index')],
            ['label' => __('sn-cms::cms.frontend.post_detail'), 'url' => Utils::route('posts.show', $this->slug)],
        ];

        return view($this->getThemeView('post.post'), [
            'breadcrumbs' => $breadcrumbs,
        ])->layout(Utils::getLayout());
    }
}
