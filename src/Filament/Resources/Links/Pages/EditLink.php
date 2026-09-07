<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Wsmallnews\Cms\Filament\Resources\Links\LinkResource;

class EditLink extends EditRecord
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
