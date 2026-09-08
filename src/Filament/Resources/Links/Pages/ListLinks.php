<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Wsmallnews\Cms\Filament\Resources\Links\LinkResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class ListLinks extends ListRecords
{
    use Scopeable;

    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
