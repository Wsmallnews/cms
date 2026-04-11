<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;

class IndexPosts extends Base
{
    use CanBeContained;

    public int $limit = 10;

    public function render()
    {
        $posts = Utils::getPostModel()::snScope(...$this->getScopeable())->published()
            ->with(['media'])
            ->orderBy('order_column', 'desc')
            ->orderBy('id', 'desc')
            ->limit($this->limit)
            ->get();

        return view($this->getThemeView('components.post.index-posts'), [
            'posts' => $posts,
        ]);
    }
}
