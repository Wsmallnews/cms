<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Table;
use Wsmallnews\Cms\Enums\LinkStatus;
use Wsmallnews\Support\Filament\Actions\ActionComponents;
use Wsmallnews\Support\Filament\Filters\FilterComponents;

class LinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sn-cms::cms.link_table.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('sn-cms::cms.link_table.url'))
                    ->limit(40)
                    ->copyable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('group_name')
                    ->label(__('sn-cms::cms.link_table.group_name'))
                    ->badge()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('nofollow')
                    ->label(__('sn-cms::cms.link_table.nofollow'))
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('order_column')
                    ->label(__('sn-cms::cms.link_table.order'))
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('sn-cms::cms.link_table.status'))
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sn-cms::cms.link_table.created_at'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('sn-cms::cms.link_table.updated_at'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->reorderable('order_column', direction: 'desc')
            ->defaultSort('order_column', 'desc')
            ->searchPlaceholder(__('sn-cms::cms.link_table.search_placeholder'))
            ->filtersFormWidth(Width::Medium)
            ->filters([
                FilterComponents::statusFilter(LinkStatus::class),
                ...FilterComponents::createUpdateRangeFilter(),
            ])
            ->recordActions([
                ...ActionComponents::recordActions([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                ...ActionComponents::toolbarActions([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
