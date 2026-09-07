<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Wsmallnews\Cms\Filament\Resources\Links\LinkResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class CreateLink extends CreateRecord
{
    use Scopeable;

    protected static string $resource = LinkResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 合并 scopeable 参数
        $data = array_merge($data, static::getScopeable());

        return parent::mutateFormDataBeforeCreate($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create')->label(__('sn-cms::cms.link_resource.action_create')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
