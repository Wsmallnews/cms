<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Actions\ActionComponents;
use Wsmallnews\Support\Filament\Filters\FilterComponents;
use Wsmallnews\Support\Filament\Resources\ScheduledTasks\Concerns\ViewScheduledTasksAction;
use Wsmallnews\Support\Filament\Tables\ColumnComponents;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                ColumnComponents::modelColumn(
                    'title',
                    __('sn-cms::cms.posts_table.title'),
                    fn ($record) => $record,
                )->searchable(['title', 'description']),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('sn-cms::cms.posts_table.categories'))
                    ->searchable()
                    ->toggleable()
                    ->badge(),
                ColumnComponents::morphColumn(
                    'publisher_type',
                    __('sn-cms::cms.posts_table.publisher'),
                    fn ($record) => $record->publisher,
                    fn ($record) => $record->publisher_type,
                    fn ($record) => $record->publisher_id,
                ),
                // flags 为数组 state，badge 模式下逐值渲染，闭包按单个 flag 值解析 enum 元数据
                Tables\Columns\TextColumn::make('flags')
                    ->label(__('sn-cms::cms.posts_table.flags'))
                    ->toggleable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => Utils::getFlagEnum()::from($state)->getLabel())
                    ->color(fn ($state) => Utils::getFlagEnum()::from($state)->getColor())
                    ->icon(fn ($state) => Utils::getFlagEnum()::from($state)->getIcon()),
                // Tables\Columns\SpatieTagsColumn::make('tags')
                //     ->label('标签')
                //     ->type('post_tags')
                //     ->toggleable(),
                Tables\Columns\TextColumn::make('counter')
                    ->label(__('sn-cms::cms.posts_table.views'))
                    ->formatStateUsing(fn ($state) => $state->view_num)
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('order_column')
                    ->label(__('sn-cms::cms.posts_table.order'))
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('sn-cms::cms.posts_table.status'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('sn-cms::cms.posts_table.published_at'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sn-cms::cms.posts_table.created_at'))
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('sn-cms::cms.posts_table.updated_at'))
                    ->toggleable()
                    ->sortable(),
            ])
            ->reorderable('order_column')
            ->defaultSort('order_column', 'asc')
            ->searchPlaceholder(__('sn-cms::cms.posts_table.search_placeholder'))
            ->filtersFormWidth(Width::Medium)
            ->filters([
                Tables\Filters\SelectFilter::make('flag')
                    ->label(__('sn-cms::cms.posts_table.flag_filter'))
                    ->options(Utils::getFlagEnum())
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->hasFlag($data['value']);
                        }
                    }),
                ...FilterComponents::createUpdateRangeFilter(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ...ActionComponents::recordActions([
                    ViewAction::make(),
                    EditAction::make(),
                    ViewScheduledTasksAction::make()->color('info'),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                ...ActionComponents::toolbarActions([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
