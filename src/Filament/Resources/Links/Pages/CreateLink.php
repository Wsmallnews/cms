<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Pages;

use Filament\Resources\Pages\CreateRecord;
use Wsmallnews\Cms\Filament\Resources\Links\LinkResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\ForwardsResourceScope;

class CreateLink extends CreateRecord
{
    use ForwardsResourceScope;

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
}
