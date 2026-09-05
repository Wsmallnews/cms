<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Livewire\Concerns\CanAddComment;
use Wsmallnews\Comment\Livewire\Concerns\HasComment;
use Wsmallnews\Comment\Livewire\Concerns\HasCommentStatus;
use Wsmallnews\Support\Facades\Seo;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;
use Wsmallnews\Support\Livewire\Concerns\HasAuth;
use Wsmallnews\Support\Livewire\Concerns\HasContentType;

class Post extends Base
{
    use CanAddComment;
    use CanBeContained;
    use HasAuth;
    use HasComment;
    use HasCommentStatus;
    use HasContentType;

    public string $slug;

    public function render()
    {
        $model = new (Utils::getPostModel());
        $post = $model->snScope(...$this->getScopeable())->published()->with(['media', 'content', 'publisher'])->where($model->getRouteKeyName(), $this->slug)->firstOrFail();

        // 增加浏览量
        $post->view($this->getAuthUser());

        // 文章页 SEO：标题/描述/封面用文章自身数据，article() 自动组装结构化数据（author = 发布者）并切 og:type
        Seo::title($post->title)
            ->description($post->description)
            ->image($post->getSnSubjectCoverUrl())
            ->article([
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'author' => $post->publisher?->name,
            ]);

        return view($this->getThemeView('components.post.post'), [
            'post' => $post,
        ]);
    }
}
