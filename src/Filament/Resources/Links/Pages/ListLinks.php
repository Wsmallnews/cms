<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Wsmallnews\Cms\Filament\Resources\Links\LinkResource;

class ListLinks extends ListRecords
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
