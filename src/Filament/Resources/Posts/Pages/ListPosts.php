<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class ListPosts extends ListRecords
{
    use Scopeable;

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

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make()
                ->label(__('sn-cms::cms.post_resource.tabs.all'))
                ->badge(fn (): int => $this->getCount()),
        ];

        foreach (PostStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make()
                ->label($status->getLabel())
                ->badge(fn (): int => $this->getCount($status))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', $status));
        }

        return $tabs;
    }

    protected function getCount(?PostStatus $status = null): int
    {
        $query = static::getResource()::getEloquentQuery()->withoutTrashed();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->count();
    }
}
