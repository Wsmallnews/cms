<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\CanBeContained;
use Wsmallnews\Cms\Support\Utils;

class IndexPosts extends Base
{
    use CanBeContained;

    public int $limit = 10;

    public function render()
    {
        $posts = Utils::getPostModel()::snScope(...$this->getScopeable())->normal()
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
