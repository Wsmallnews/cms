<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\NavigationManage as NavigationManageWidgets;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;

class EditNavigationType extends EditRecord
{
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
        return [
            NavigationManageWidgets::class,
        ];
    }
}
