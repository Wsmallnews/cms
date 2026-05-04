<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Schemas;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Facades\FlagRegistry;
use Wsmallnews\Support\Support\Utils as SupportUtils;

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
                            ->relationship(relationship: 'categories', titleAttribute: 'name', parentAttribute: 'parent_id', modifyQueryUsing: function ($query, Component $livewire) {
                                return $query->scopeable($livewire::getScopeType(), $livewire::getScopeId());
                            }, modifyChildQueryUsing: function ($query, Component $livewire) {
                                return $query->scopeable($livewire::getScopeType(), $livewire::getScopeId());
                            })
                            ->searchable()
                            ->enableBranchNode()
                            ->withCount()
                            ->placeholder(__('请选择图文分类'))
                            ->emptyLabel(__('未搜索到分类'))
                            ->treeKey('postCategories'),

                        Forms\Components\TextInput::make('title')->label('标题')
                            ->placeholder('请输入内容标题')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Str::slug(title: $state, language: app()->getLocale()));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->scopedUnique(modifyQueryUsing: function (Builder $query, Component $livewire) {
                                return $query->scopeable($livewire::getScopeType(), $livewire::getScopeId());
                            })
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')->label('描述')
                            ->placeholder('请输入描述'),
                    ])->columns(1),
                    Schemas\Components\Section::make('内容')->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('post_image')
                            ->label('主图')->required()
                            ->collection('post_image')
                            ->image()
                            ->disk(SupportUtils::getFilesystemDisk())
                            ->visibility('public')
                            ->customProperties(function (Component $livewire) {
                                return [
                                    ...$livewire::getScopeable(),
                                    'team_id' => current_tenant()?->id,
                                ];
                            })
                            ->openable()
                            ->downloadable()
                            ->uploadingMessage('主图上传中...')
                            ->imagePreviewHeight('200'),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('post_images')
                            ->label('轮播图')
                            ->collection('post_images')
                            ->image()
                            ->disk(SupportUtils::getFilesystemDisk())
                            ->visibility('public')
                            ->customProperties(function (Component $livewire) {
                                return [
                                    ...$livewire::getScopeable(),
                                    'team_id' => current_tenant()?->id,
                                ];
                            })
                            ->multiple()
                            ->openable()
                            ->downloadable()
                            ->reorderable()
                            ->appendFiles()
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
                    // Forms\Components\SpatieTagsInput::make('tags')->label('标签')->type(function (Component $livewire) {
                    //     return $livewire::getResource()::getTagType();
                    // }),
                    Forms\Components\ToggleButtons::make('flags')
                        ->label('标志')
                        ->multiple()
                        ->inline()
                        ->options(fn(Component $livewire) => FlagRegistry::getTypesOptions($livewire::getScopeType()))
                        ->colors(fn(Component $livewire) => FlagRegistry::getTypesColors($livewire::getScopeType()))
                        ->icons(fn(Component $livewire) => FlagRegistry::getTypesIcons($livewire::getScopeType())),
                    Forms\Components\TextInput::make('order_column')->label('排序')->integer()
                        ->placeholder('正序排列')
                        ->rules(['integer', 'min:0']),
                    Forms\Components\ToggleButtons::make('status')
                        ->label('状态')
                        ->default(PostStatus::Published)
                        ->inline()
                        ->options(PostStatus::class),
                    Schemas\Components\Group::make()
                        ->schema([
                            Forms\Components\Radio::make('scheduled_at_type')
                                ->label('计划发布时间类型')
                                ->default('scheduled_at')
                                ->inline()
                                ->options([
                                    'scheduled_at' => '计划发布时间',
                                    'minutes_later' => '分钟后发布',
                                ]),
                            Forms\Components\DateTimePicker::make('scheduled_at')
                                ->label('计划发布时间')
                                ->placeholder('选择发布时间')
                                ->displayFormat('Y-m-d H:i:s')
                                ->native(false)
                                ->required(fn(Get $get) => (bool) ($get('status') == PostStatus::Scheduled && $get('scheduled_at_type') === 'scheduled_at'))
                                ->markAsRequired()
                                ->visibleJs(<<<'JS'
                                    $get('scheduled_at_type') == 'scheduled_at'
                                JS),
                            Forms\Components\TextInput::make('minutes_later')
                                ->label('分钟后发布')
                                ->placeholder('请输入分钟数')
                                ->integer()
                                ->minValue(0)
                                ->required(fn(Get $get) => (bool) ($get('status') == PostStatus::Scheduled && $get('scheduled_at_type') === 'minutes_later'))
                                ->markAsRequired()
                                ->visibleJs(<<<'JS'
                                    $get('scheduled_at_type') == 'minutes_later'
                                JS),
                        ])->visibleJs(<<<'JS'
                            $get('status') == 'scheduled'
                        JS),
                ])->grow(false),
            ])
                ->columnSpanFull()
                ->from('lg'),
        ];
    }
}
