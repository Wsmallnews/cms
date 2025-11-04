<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePost extends CreateRecord
{
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
        $data = array_merge($data, static::getResource()::getScopeInfo());

        return parent::mutateFormDataBeforeCreate($data);
    }
}
