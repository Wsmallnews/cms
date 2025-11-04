<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;

class ListPosts extends ListRecords
{
    public int $category_id = 0;

    protected static string $resource = PostResource::class;

    public function mount(): void
    {
        $this->category_id = request()->query('category_id', 0);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    // protected function getTableQuery(): ?Builder
    // {
    //     $query = static::getResource()::getEloquentQuery();

    //     if ($this->category_id) {
    //         // 可多选时候的条件
    //         $childrenIds = (new Tree(function () {
    //             return Category::query();
    //         }))->getChildFields($this->category_id);

    //         // $childrenIds = array_map('strval', $childrenIds);

    //         // $query->whereCategoryIn($childrenIds);

    //         // 分类单选时候的条件
    //         $query->whereIn('category_id', $childrenIds);
    //     }

    //     return $query;
    // }

    /**
     * @return array<NavigationItem | NavigationGroup>
     */
    // public function getSubNavigation(): array
    // {
    //     $categories = (new Tree(function () {
    //         return Category::query()->orderBy('order_column')->orderBy('id', 'asc');
    //     }))->getTree(resultCb: function ($trees) use (&$navigationItems) {
    //         return $trees->map(function ($tree) {
    //             if ($translatableContentDriver = $this->makeFilamentTranslatableContentDriver()) {
    //                 return $translatableContentDriver->setRecordLocale($tree);
    //             }
    //         });
    //     });

    //     $navigations = $this->getNavigationsByCategories($categories);
    //     return $navigations;
    // }

    // private function getNavigationsByCategories(Collection $categories): array
    // {
    //     $navigations[] = NavigationItem::make()
    //         ->label('全部')
    //         ->icon('heroicon-o-rectangle-stack')
    //         ->url(self::getUrl())
    //         ->isActiveWhen(fn(): bool => $this->category_id == 0);

    //     $navigationItems = $this->getNavigationItemsByCategories($categories);

    //     $navigations[] = NavigationGroup::make()
    //         ->label('筛选分类')
    //         ->items($navigationItems);

    //     return $navigations;
    // }

    // private function getNavigationItemsByCategories(Collection $categories)
    // {
    //     $navigations = $categories->map(function ($category) {
    //         $current = NavigationItem::make()
    //             ->label($category->name)
    //             ->icon('heroicon-o-rectangle-stack')
    //             ->url(self::getUrl(['category_id' => $category->id]))
    //             ->isActiveWhen(fn(): bool => $this->category_id == $category->id);

    //         if (isset($category->children) && $category->children->isNotEmpty()) {
    //             $current->childItems($this->getNavigationItemsByCategories($category->children));
    //         }

    //         return $current;
    //     });

    //     return $navigations->toArray();
    // }
}
