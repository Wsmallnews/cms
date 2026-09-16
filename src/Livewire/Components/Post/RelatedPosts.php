<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;

/**
 * 相关文章推荐：来源回退链——显式配置的分类（categoryIds）> 同行文章组件提供的当前文章（$post）> 空态。
 * $post 由编排渲染器经上下文注入（编排条目声明 context => ['post']），也可由调用处显式传入
 */
class RelatedPosts extends Base
{
    use CanBeContained;

    public ?array $categoryIds = null;

    public ?object $post = null;

    public ?int $limit = 6;

    public function render()
    {
        $posts = $this->getRelatedPosts();

        return view($this->getThemeView('components.post.related-posts'), [
            'posts' => $posts,
            'hasSource' => filled($posts) || $this->hasSource(),
        ]);
    }

    protected function hasSource(): bool
    {
        return filled($this->categoryIds) || filled($this->post?->id);
    }

    protected function getRelatedPosts()
    {
        // 来源优先级：显式配置分类 > 当前文章的分类；无来源返回空集合（渲染空态提示）
        $categoryIds = filled($this->categoryIds) ? $this->categoryIds : $this->post?->categories?->pluck('id')->all();

        if (blank($categoryIds)) {
            return collect();
        }

        return Utils::getPostModel()::query()
            ->published()
            ->snScope(...$this->getScopeable())
            ->categoryIds($categoryIds)
            ->when(filled($this->post?->id), fn ($query) => $query->whereKeyNot($this->post->id))
            ->with(['media'])
            ->orderBy('order_column', 'desc')
            ->orderBy('id', 'desc')
            ->limit($this->limit ?? 6)
            ->get();
    }
}
