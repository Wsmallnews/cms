<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;

class CreateNavigationType extends CreateRecord
{
    protected static string $resource = NavigationTypeResource::class;

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
