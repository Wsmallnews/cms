<?php

namespace Wsmallnews\Cms\Livewire\Components\Post;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\WithoutUrlPagination;
use Wsmallnews\Category\Livewire\Concerns\Categoryable;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;
use Wsmallnews\Support\Livewire\Concerns\CanPagination;

class Posts extends Base
{
    use CanBeContained;
    use CanPagination;
    use Categoryable;
    use WithoutUrlPagination;

    public int | array | null $categoryIds = [];

    #[Url(except: 0)]
    public int $categoryId = 0;

    public Collection $posts;

    #[Url(except: '')]
    public string $flag = '';

    public string $categoryStyle = 'select';

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
        // 根据 categoryStyle 类型读取特定的参数
        $categoryIds = $this->categoryStyle == 'select' ? Arr::wrap($this->categoryIds) : Arr::wrap($this->categoryId);

        $categories = filled($categoryIds) ? $this->getScopedQuery()->normal()->whereIn('id', $categoryIds)->get() : collect([]);

        // 获取传入的分类的 id 以及所有子分类的 id
        $allCategories = filled($categoryIds) ? $this->getCategoryIds($categories) : collect([]);

        // 查询图文
        $query = Utils::getPostModel()::snScope(...$this->getScopeable())->published()->with(['media'])
            ->when($allCategories->isNotEmpty(), function ($query) use ($allCategories) {
                $query->categoryIds($allCategories);
            })
            ->when($this->flag, function ($query) {
                $query->hasFlag($this->flag);
            })
            ->when($this->flag != 'top', function ($query) {
                $query->orderByRaw('JSON_CONTAINS(flags, \'"top"\') DESC');
            })
            ->orderBy('order_column', 'desc')
            ->orderBy('id', 'desc');

        // 分页
        $this->posts = $this->withPagination($query);

        return view($this->getThemeView('components.post.posts'), [
            'categories' => $categories,
            'paginatorLink' => $this->links,
        ]);
    }

    /**
     * 获取指定分类的所有下级分类的 id
     *
     * @param  int|array|null  $categoryIds
     * @return Collection
     */
    protected function getCategoryIds($categories)
    {
        $allCategories = collect([]);       // 要查询的分类，以及分类的所有子节点
        foreach ($categories as $category) {
            // 查询分类以及分类的所有子节点
            $currentIds = $category->descendants()->pluck('id');
            $allCategories = $allCategories->merge($currentIds);
        }
        $allCategories = $allCategories->merge($categories->pluck('id'));
        $allCategories = $allCategories->filter()->unique()->values();

        return $allCategories;
    }
}
