<?php

namespace Wsmallnews\Cms\Livewire\Post\Components;

use Wsmallnews\Cms\Models\Post as PostModel;
use Illuminate\Database\Eloquent\Model;
use Wsmallnews\Cms\Livewire\Common\Components\BaseComponent;

class Post extends BaseComponent
{
    public int $id;

    public string $wrapperView = 'base.block';

    public function render()
    {
        $post = PostModel::query()->scopeTenant()->normal()->with(['media', 'content'])->findOrFail($this->id);

        Model::withoutTimestamps(fn() => $post->increment('views'));        // 增加浏览量,不更新 updated_at

        return view('sn-cms::livewire.components.post', [
            'post' => $post
        ]);
    }
}
