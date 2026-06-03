<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\Navigation as NavigationWidget;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class EditNavigationType extends EditRecord
{
    use Scopeable;

    protected static string $resource = NavigationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        $record = $this->getRecord();

        return [
            NavigationWidget::make([
                'key' => 'widgets-' . $record?->id . '-' . $record?->level,
            ]),
        ];
    }
}
