<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Schemas;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Facades\FlagRegistry;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\ScheduledTask;
use Wsmallnews\Support\Filament\Forms\FormComponents;

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
                    Schemas\Components\Section::make(__('sn-cms::cms.post_form.basic_info'))->schema([
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
                        SelectTree::make('categories')->label(__('sn-cms::cms.post_form.categories'))
                            ->relationship(relationship: 'categories', titleAttribute: 'name', parentAttribute: 'parent_id', modifyQueryUsing: function ($query, Component $livewire) {
                                return $query->scopeable($livewire::getScopeType(), $livewire::getScopeId());
                            }, modifyChildQueryUsing: function ($query, Component $livewire) {
                                return $query->scopeable($livewire::getScopeType(), $livewire::getScopeId());
                            })
                            ->searchable()
                            ->enableBranchNode()
                            ->withCount()
                            ->placeholder(__('sn-cms::cms.post_form.categories_placeholder'))
                            ->emptyLabel(__('sn-cms::cms.post_form.categories_empty'))
                            ->treeKey('postCategories'),

                        Forms\Components\TextInput::make('title')->label(__('sn-cms::cms.post_form.title'))
                            ->placeholder(__('sn-cms::cms.post_form.title_placeholder'))
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
                        Forms\Components\Textarea::make('description')->label(__('sn-cms::cms.post_form.description'))
                            ->placeholder(__('sn-cms::cms.post_form.description_placeholder')),
                    ])->columns(1),
                    Schemas\Components\Section::make(__('sn-cms::cms.post_form.content_section'))->schema([
                        FormComponents::mediaImageUpload('post_image', 'post_image')
                            ->label(__('sn-cms::cms.post_form.main_image'))->required()
                            ->customProperties(function (Component $livewire) {
                                return [
                                    ...$livewire::getScopeable(),
                                    'team_id' => current_tenant()?->id,
                                ];
                            })
                            ->uploadingMessage(__('sn-cms::cms.post_form.main_image_uploading')),
                        FormComponents::mediaImageUpload('post_images', 'post_images')
                            ->label(__('sn-cms::cms.post_form.carousel_images'))
                            ->customProperties(function (Component $livewire) {
                                return [
                                    ...$livewire::getScopeable(),
                                    'team_id' => current_tenant()?->id,
                                ];
                            })
                            ->multiple()
                            ->uploadingMessage(__('sn-cms::cms.post_form.carousel_images_uploading')),
                        FormComponents::contentTypeGroup(
                            types: Utils::getConfig('contents.post.types'),
                            defaultType: Utils::getConfig('contents.post.default_type'),
                            directory: Utils::getFileDirectory('contents'),
                        ),
                    ])->columns(1),
                    Schemas\Components\Section::make(__('sn-support::support.scheduled_task.label'))->schema([
                        ScheduledTask::scheduleRepeater('sn_post'),
                    ])->columns(1),
                ])->columns(1),
                Schemas\Components\Section::make(__('sn-cms::cms.post_form.status_section'))->schema([
                    // Forms\Components\SpatieTagsInput::make('tags')->label('标签')->type(function (Component $livewire) {
                    //     return $livewire::getResource()::getTagType();
                    // }),
                    Forms\Components\ToggleButtons::make('flags')
                        ->label(__('sn-cms::cms.post_form.flags'))
                        ->multiple()
                        ->inline()
                        ->options(fn (Component $livewire) => FlagRegistry::getTypesOptions($livewire::getScopeType()))
                        ->colors(fn (Component $livewire) => FlagRegistry::getTypesColors($livewire::getScopeType()))
                        ->icons(fn (Component $livewire) => FlagRegistry::getTypesIcons($livewire::getScopeType())),
                    Forms\Components\TextInput::make('order_column')->label(__('sn-cms::cms.post_form.order'))->integer()
                        ->placeholder(__('sn-cms::cms.post_form.order_placeholder'))
                        ->rules(['integer', 'min:0']),
                    Forms\Components\ToggleButtons::make('status')
                        ->label(__('sn-cms::cms.post_form.status'))
                        ->default(PostStatus::Published)
                        ->inline()
                        ->options(PostStatus::class),
                ])->grow(false),
            ])
                ->columnSpanFull()
                ->from('lg'),
        ];
    }
}
