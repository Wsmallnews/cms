<?php

namespace Wsmallnews\Cms\Livewire\Post\Components;

use Wsmallnews\Cms\Livewire\Common\Components\BaseComponent;
use Wsmallnews\Cms\Models\Post;

class IndexPosts extends BaseComponent
{
    public int $limit = 10;

    public string $wrapperView = 'base.empty-block';

    public string $itemWrapperView = 'base.block';

    public function render()
    {
        $posts = Post::query()->normal()->limit($this->limit)->get();

        return view('sn-cms::livewire.components.index-posts', [
            'posts' => $posts,
        ]);
    }
}
