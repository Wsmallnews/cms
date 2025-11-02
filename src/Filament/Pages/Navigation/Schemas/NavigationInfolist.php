<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Schemas;

use Filament\Infolists;

class NavigationInfolist
{
    public static function infolist(): array
    {
        return [
            Infolists\Components\TextEntry::make('description')
                ->label('描述')
                ->visible(fn ($state): bool => $state ? true : false),
            Infolists\Components\IconEntry::make('status')
                ->label('状态'),
        ];
    }
}
