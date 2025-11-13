<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Illuminate\Database\Eloquent\Model;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Post as PostModel;

class Post extends Base
{
    public int $id;

    public string $wrapperView = 'sn-cms::base.block';

    public function render()
    {
        // $post = PostModel::snScope(...$this->getScopeable())->normal()->with(['media', 'content'])->findOrFail($this->id);
        $post = PostModel::snScope(...$this->getScopeable())->normal()->with(['content'])->findOrFail($this->id);

        Model::withoutTimestamps(fn () => $post->increment('views'));        // 增加浏览量,不更新 updated_at

        return view('sn-cms::livewire.components.post.post', [
            'post' => $post,
        ]);
    }
}
