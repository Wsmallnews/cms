<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Schemas;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Livewire\Component;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...static::forms(),
            ]);
    }

    public static function forms(): array
    {
        return [
            Schemas\Components\Flex::make([
                Schemas\Components\Group::make()->schema([
                    Schemas\Components\Section::make('基础信息')->schema([
                        // 单选 分类
                        // SelectTree::make('category_id')->label('选择分类')
                        //     ->relationship(relationship: 'category', titleAttribute: 'name', parentAttribute: 'parent_id')
                        //     ->searchable()
                        //     ->parentNullValue(0)
                        //     ->enableBranchNode()
                        //     ->withCount()
                        //     // ->placeholder(__('请选择图文分类'))
                        //     // ->emptyLabel(__('未搜索到分类'))
                        //     ->treeKey('postCategoryId')
                        //     ,

                        // 多选分类
                        SelectTree::make('categories')->label('选择分类')
                            ->relationship(relationship: 'categories', titleAttribute: 'name', parentAttribute: 'parent_id', modifyQueryUsing: function ($query) {
                                return $query->scopeable(PostResource::getScopeType(), PostResource::getScopeId());
                            }, modifyChildQueryUsing: function ($query) {
                                return $query->scopeable(PostResource::getScopeType(), PostResource::getScopeId());
                            })
                            ->searchable()
                            ->enableBranchNode()
                            ->withCount()
                            ->placeholder(__('请选择图文分类'))
                            ->emptyLabel(__('未搜索到分类'))
                            ->treeKey('postCategories'),

                        Forms\Components\TextInput::make('title')->label('标题')
                            ->placeholder('请输入内容标题')
                            ->required(),
                        Forms\Components\Textarea::make('description')->label('描述')
                            ->placeholder('请输入描述'),
                    ])->columns(1),
                    Schemas\Components\Section::make('内容')->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('post_image')->label('主图')
                            ->collection('post_image')
                            ->customProperties(function (Component $livewire) {
                                return $livewire->getScopeable();;
                            })
                            ->required()
                            ->image()
                            ->visibility('public')
                            ->openable()
                            ->downloadable()
                            ->uploadingMessage('主图上传中...')
                            ->imagePreviewHeight('200'),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('post_images')->label('轮播图')
                            ->collection('post_images')
                            ->customProperties(function (Component $livewire) {
                            return $livewire->getScopeable();
                            })
                            ->image()
                            ->visibility('public')
                            ->multiple()
                            ->openable()
                            ->downloadable()
                            ->reorderable()
                            ->appendFiles()
                            ->minFiles(1)
                            ->maxFiles(20)
                            ->uploadingMessage('轮播图片上传中...')
                            ->imagePreviewHeight('200'),
                        Schemas\Components\Group::make()
                            ->relationship('content')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->fileAttachmentsDirectory('contents/' . date('Ymd'))
                                    ->label('内容详情'),
                            ])->columns(1),
                    ])->columns(1),
                ])->columns(1),
                Schemas\Components\Section::make('状态')->schema([
                    // Forms\Components\SpatieTagsInput::make('tags')->label('标签')->type('post_tags'),
                    Forms\Components\TextInput::make('order_column')->label('排序')->integer()
                        ->placeholder('正序排列')
                        ->rules(['integer', 'min:0']),
                    Forms\Components\Radio::make('status')
                        ->label('状态')
                        ->default(PostStatus::Normal)
                        ->inline()
                        ->options(PostStatus::class),
                ])->grow(false),
            ])
                ->columnSpanFull()
                ->from('lg'),
        ];
    }
}
