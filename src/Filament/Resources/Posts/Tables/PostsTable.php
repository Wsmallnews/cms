<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
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
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->description(fn ($record) => $record->description)
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                // Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                //     ->label('主图')
                //     ->collection('main')
                //     ->toggleable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('分类')
                    ->searchable()
                    ->toggleable()
                    ->badge(),
                Tables\Columns\ViewColumn::make('publisher')
                    ->label('发布者')
                    ->toggleable()
                    ->view('sn-cms::filament.tables.columns.publisher'),
                Tables\Columns\ViewColumn::make('flags')
                    ->label('标志')
                    ->toggleable()
                    ->view('sn-cms::filament.tables.columns.flags-text', function (Component $livewire) {
                        return ['scopeType' => $livewire::getScopeType()];
                    }),
                // Tables\Columns\SpatieTagsColumn::make('tags')
                //     ->label('标签')
                //     ->type('post_tags')
                //     ->toggleable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('浏览量')
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('order_column')
                    ->label('排序')
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->toggleable()
                    ->sortable(),
            ])
            ->reorderable('order_column')
            ->defaultSort('order_column', 'asc')
            ->searchPlaceholder('搜索标题、描述等...')
            ->filtersFormWidth(Width::Medium)
            ->filters([
                Tables\Filters\SelectFilter::make('flag')
                    ->label('标志')
                    ->options(fn (Component $livewire) => FlagRegistry::getTypesOptions($livewire::getScopeType()))
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->hasFlag($data['value']);
                        }
                    }),
                ...FilamentHelper::createUpdateRangeFilter(),
                TrashedFilter::make(),
            ])
            ->recordActions([
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
