<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Schemas;

use Filament\Infolists;

class NavigationInfolist
{
    public static function infolist(): array
    {
        return [
            Infolists\Components\TextEntry::make('type')
                ->label(__('sn-cms::cms.navigation_infolist.type')),
            Infolists\Components\TextEntry::make('description')
                ->label(__('sn-cms::cms.navigation_infolist.description'))
                ->visible(fn ($state): bool => $state ? true : false),
            Infolists\Components\IconEntry::make('status')
                ->label(__('sn-cms::cms.navigation_infolist.status')),
        ];
    }
}
