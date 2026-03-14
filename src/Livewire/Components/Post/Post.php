<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Illuminate\Database\Eloquent\Model;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\CanBeContained;
use Wsmallnews\Cms\Support\Utils;

class Post extends Base
{
    use CanBeContained;

    public string $slug;

    public function render()
    {
        $model = new (Utils::getPostModel());
        $post = $model->snScope(...$this->getScopeable())->published()->with(['media', 'content'])->where($model->getRouteKeyName(), $this->slug)->firstOrFail();

        Model::withoutTimestamps(fn () => $post->increment('views'));        // 增加浏览量,不更新 updated_at

        return view($this->getThemeView('components.post.post'), [
            'post' => $post,
        ]);
    }
}
