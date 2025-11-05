<?php

namespace Wsmallnews\Cms\Livewire\Post\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\WithoutUrlPagination;
use Wsmallnews\Category\Models\Category as CategoryModel;
use Wsmallnews\Cms\Livewire\Common\Components\BaseComponent;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Support\Livewire\Traits\CanPagination;

class Posts extends BaseComponent
{
    use CanPagination;
    use WithoutUrlPagination;

    public int | array $category_ids = [];      // @sn todo 有时间把这个改为驼峰

    public Collection $posts;

    public string $wrapperView = 'base.empty-block';

    public string $itemWrapperView = 'base.block';

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
        $categoryIds = Arr::wrap($this->category_ids);

        $allCategories = collect([]);       // 要查询的分类，以及分类的所有子节点
        foreach ($categoryIds as $id) {
            // 查询分类以及分类的所有子节点
            $currentIds = CategoryModel::scoped(has_tenancy() ? ['team_id' => current_tenant()->id] : [])->descendantsAndSelf($id)->pluck('id');
            $allCategories = $allCategories->merge($currentIds);
        }
        $allCategories = $allCategories->filter()->unique()->values();

        // 查询图文
        $query = PostModel::query()->scopeTenant()->normal()->with(['media'])->when($allCategories->isNotEmpty(), function ($query) use ($allCategories) {
            $query->whereCategoryIn($allCategories);
        })->orderBy('order_column', 'desc');

        // 分页
        $this->posts = $this->withPagination($query);

        return view('sn-cms::livewire.components.posts', [
            'paginatorLink' => $this->links,
        ]);
    }
}
