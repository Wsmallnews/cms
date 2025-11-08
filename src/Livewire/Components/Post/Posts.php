<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\WithoutUrlPagination;
use Wsmallnews\Category\Livewire\Concerns\Categoryable;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Support\Livewire\Concerns\CanPagination;

class Posts extends Base
{
    use CanPagination;
    use Categoryable;
    use WithoutUrlPagination;

    public int | array | null $categoryIds = [];

    public Collection $posts;

    public string $wrapperView = 'sn-support::base.empty-block';

    public string $itemWrapperView = 'sn-support::base.block';

    public function mount()
    {
        $this->posts = $this->posts ?? collect([]);
    }

    protected function getCurrents()
    {
        return $this->posts;
    }

    public function render()
    {
        $allCategories = $this->getCategoryIds($this->categoryIds);

        // 查询图文
        // $query = PostModel::query()->scopeTenant()->normal()->with(['media'])->when($allCategories->isNotEmpty(), function ($query) use ($allCategories) {
        $query = PostModel::snScope(...$this->getScopeable())->normal()->when($allCategories->isNotEmpty(), function ($query) use ($allCategories) {
            $query->whereCategoryIn($allCategories);
        })->orderBy('order_column', 'desc');

        // 分页
        $this->posts = $this->withPagination($query);

        return view('sn-cms::livewire.components.post.posts', [
            'paginatorLink' => $this->links,
        ]);
    }

    /**
     * 获取指定分类的所有下级分类的 id
     *
     * @param  int|array|null  $categoryIds
     * @return Collection
     */
    protected function getCategoryIds($categoryIds)
    {
        $categoryIds = Arr::wrap($categoryIds);

        $allCategories = collect([]);       // 要查询的分类，以及分类的所有子节点
        foreach ($categoryIds as $id) {
            // 查询分类以及分类的所有子节点
            $currentIds = $this->getScopedQuery()->normal()->descendantsAndSelf($id)->pluck('id');
            $allCategories = $allCategories->merge($currentIds);
        }
        $allCategories = $allCategories->filter()->unique()->values();

        return $allCategories;
    }
}
