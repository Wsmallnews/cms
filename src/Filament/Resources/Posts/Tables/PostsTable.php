<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Livewire\Component;
use Wsmallnews\Cms\Facades\FlagRegistry;
use Wsmallnews\Support\Helpers\FilamentHelper;

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
                Tables\Columns\TextColumn::make('title')
                    ->label(__('sn-cms::cms.posts_table.title'))
                    ->searchable()
                    ->view('sn-cms::filament.tables.columns.post-title'),
                // Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                //     ->label('主图')
                //     ->collection('main')
                //     ->toggleable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('sn-cms::cms.posts_table.categories'))
                    ->searchable()
                    ->toggleable()
                    ->badge(),
                Tables\Columns\ViewColumn::make('publisher')
                    ->label(__('sn-cms::cms.posts_table.publisher'))
                    ->toggleable()
                    ->view('sn-cms::filament.tables.columns.publisher'),
                Tables\Columns\ViewColumn::make('flags')
                    ->label(__('sn-cms::cms.posts_table.flags'))
                    ->toggleable()
                    ->view('sn-cms::filament.tables.columns.flags-text', function (Component $livewire) {
                        return ['scopeType' => $livewire::getScopeType()];
                    }),
                // Tables\Columns\SpatieTagsColumn::make('tags')
                //     ->label('标签')
                //     ->type('post_tags')
                //     ->toggleable(),
                Tables\Columns\TextColumn::make('counter')
                    ->label(__('sn-cms::cms.posts_table.views'))
                    ->formatStateUsing(fn($state) => $state->view_num)
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
                    ->options(fn(Component $livewire) => FlagRegistry::getTypesOptions($livewire::getScopeType()))
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->hasFlag($data['value']);
                        }
                    }),
                ...FilamentHelper::createUpdateRangeFilter(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
