<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Illuminate\Database\Eloquent\Model;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;

class Post extends Base
{
    public int $id;

    public function render()
    {
        $post = Utils::getPostModel()::snScope(...$this->getScopeable())->normal()->with(['media', 'content'])->findOrFail($this->id);

        Model::withoutTimestamps(fn () => $post->increment('views'));        // 增加浏览量,不更新 updated_at

        return view($this->getView('components.post.post'), [
            'post' => $post,
        ]);
    }
}
