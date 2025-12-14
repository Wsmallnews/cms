<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;

class IndexPosts extends Base
{
    public int $limit = 10;

    public string $wrapperView = 'sn-cms::base.empty-block';

    public string $itemWrapperView = 'sn-cms::base.block';

    public function render()
    {
        $posts = Utils::getPostModel()::snScope(...$this->getScopeable())->normal()->limit($this->limit)->get();

        return view($this->getThemeView('components.index-posts'), [
            'posts' => $posts,
        ]);
    }
}
