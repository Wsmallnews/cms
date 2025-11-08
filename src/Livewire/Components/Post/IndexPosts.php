<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Post;

class IndexPosts extends Base
{
    public int $limit = 10;

    public string $wrapperView = 'sn-support::base.empty-block';

    public string $itemWrapperView = 'sn-support::base.block';

    public function render()
    {
        $posts = Post::snScope(...$this->getScopeable())->normal()->limit($this->limit)->get();

        return view('sn-cms::livewire.components.index-posts', [
            'posts' => $posts,
        ]);
    }
}
