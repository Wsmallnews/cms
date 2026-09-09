<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Schemas;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Wsmallnews\Cms\Enums\PostStatus;
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
                    ->treeKey('postCategories')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('title')->label(__('sn-cms::cms.post_form.title'))
                    ->placeholder(__('sn-cms::cms.post_form.title_placeholder'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, $state) => $set('slug', generate_slug($state, fallbackPrefix: 'post'))),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->scopedUnique(modifyQueryUsing: function (Builder $query, Component $livewire) {
                        return $query->scopeable($livewire::getScopeType(), $livewire::getScopeId());
                    })
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')->label(__('sn-cms::cms.post_form.description'))
                    ->placeholder(__('sn-cms::cms.post_form.description_placeholder'))
                    ->columnSpanFull(),

                // Forms\Components\SpatieTagsInput::make('tags')->label('标签')->type(function (Component $livewire) {
                //     return $livewire::getResource()::getTagType();
                // }),
                Forms\Components\ToggleButtons::make('flags')
                    ->label(__('sn-cms::cms.post_form.flags'))
                    ->multiple()
                    ->inline()
                    ->grouped()
                    ->options(Utils::getFlagEnum())
                    ->columnSpanFull(),
                FormComponents::orderColumnInput(),
                FormComponents::statusToggleButtons(PostStatus::class),
            ])->columns(2)->columnSpanFull(),

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
                )->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),

            Schemas\Components\Section::make(__('sn-support::support.scheduled_task.label'))->schema([
                ScheduledTask::scheduleRepeater('sn_post')->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ];
    }
}
