<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Livewire\Concerns\CanAddComment;
use Wsmallnews\Comment\Livewire\Concerns\HasComment;
use Wsmallnews\Comment\Livewire\Concerns\HasCommentStatus;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;
use Wsmallnews\Support\Livewire\Concerns\HasAuth;
use Wsmallnews\Support\Livewire\Concerns\HasContentType;

class Post extends Base
{
    use CanBeContained;
    use CanAddComment;
    use HasComment;
    use HasCommentStatus;
    use HasContentType;
    use HasAuth;

    public string $slug;

    public function render()
    {
        $model = new (Utils::getPostModel());
        $post = $model->snScope(...$this->getScopeable())->published()->with(['media', 'content'])->where($model->getRouteKeyName(), $this->slug)->firstOrFail();

        // 增加浏览量
        $post->view($this->getAuthUser());

        return view($this->getThemeView('components.post.post'), [
            'post' => $post,
        ]);
    }
}
