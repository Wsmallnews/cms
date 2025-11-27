<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class CreatePost extends CreateRecord
{
    use Scopeable;

    protected static string $resource = PostResource::class;

    /**
     * Mutate the form data before creating a record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 合并 scopeinfo 参数
        $data = array_merge($data, static::getScopeable());

        return parent::mutateFormDataBeforeCreate($data);
    }
}
