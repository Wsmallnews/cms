<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Illuminate\Database\Eloquent\Model;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Livewire\Concerns\CanComment;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;
use Wsmallnews\Support\Livewire\Concerns\HasAuth;

class Post extends Base
{
    use CanComment;
    use CanBeContained;
    use HasAuth;

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
