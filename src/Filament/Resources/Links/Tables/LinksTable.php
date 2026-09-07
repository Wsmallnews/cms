<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Wsmallnews\Cms\Enums\LinkStatus;
use Wsmallnews\Cms\Models\Link;

class LinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sn-cms::cms.link_table.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('sn-cms::cms.link_table.url'))
                    ->limit(40)
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('group_name')
                    ->label(__('sn-cms::cms.link_table.group_name'))
                    ->badge()
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\IconColumn::make('nofollow')
                    ->label(__('sn-cms::cms.link_table.nofollow'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('order_column')
                    ->label(__('sn-cms::cms.link_table.order'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('sn-cms::cms.link_table.status'))
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sn-cms::cms.link_table.created_at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('sn-cms::cms.link_table.status'))
                    ->options(LinkStatus::class),
            ])
            ->recordUrl(null)
            ->defaultSort('order_column', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->ordered());
    }
}
