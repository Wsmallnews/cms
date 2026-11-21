<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\ForwardsResourceScope;

class ListNavigationTypes extends ListRecords
{
    use ForwardsResourceScope;

    protected static string $resource = NavigationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
