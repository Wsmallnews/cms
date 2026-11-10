<?php

namespace Wsmallnews\Cms\Boot;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Components\Post\IndexPosts;
use Wsmallnews\Cms\Livewire\Components\Post\Post;
use Wsmallnews\Cms\Livewire\Components\Post\Posts;
use Wsmallnews\Cms\Livewire\Components\Post\RelatedPosts;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\CompositionRegistry as CompositionRegistryFacade;

/**
 * Composable content types the module exposes to the composition editor.
 */
final class RegisterComposition
{
    public function register(): void
    {
        // 注册导航内容（key = 模块标识插件 id，与页面实例 scope 无关——footer 等差异实例页面共用同一套组件）
        CompositionRegistryFacade::registers(app(CmsPlugin::class)->getId(), [
            [
                'type' => 'posts',
                'label' => __('sn-cms::cms.content_type.posts'),
                // 字段平铺：外层 extras Fieldset 已统一单列（编排槽位有宽窄，注册表单不要再嵌自带列数的布局组件）
                'forms' => fn ($fields) => [
                    // 多选分类
                    ToggleButtons::make('categoryStyle')->label(__('sn-cms::cms.post_form.category_style'))
                        ->default('select')
                        ->options([
                            'select' => __('sn-cms::cms.post_form.category_style_select'),
                            'tree' => __('sn-cms::cms.post_form.category_style_tree'),
                        ])
                        ->colors([
                            'select' => 'warning',
                            'tree' => 'info',
                        ])
                        ->inline()
                        ->grouped()
                        ->helperText(__('sn-cms::cms.post_form.category_style_helper')),
                    SelectTree::make('categoryIds')->label(__('sn-cms::cms.post_form.categories'))
                        ->query(query: function () {
                            return CategoryModel::scopeable(Utils::getScopeType(), Utils::getScopeId());
                        }, titleAttribute: 'name', parentAttribute: 'parent_id')
                        ->searchable()
                        ->multiple()
                        ->enableBranchNode()
                        ->withCount()
                        ->placeholder(__('sn-cms::cms.post_form.categories_placeholder'))
                        ->emptyLabel(__('sn-cms::cms.post_form.categories_empty'))
                        ->treeKey('postCategories')
                        ->visibleJs(<<<'JS'
                            $get('categoryStyle') == 'select'
                        JS),
                ],
                'components' => [
                    Posts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
            [
                'type' => 'index-posts',
                'label' => __('sn-cms::cms.content_type.index_posts'),
                'forms' => fn ($fields) => [],
                'components' => [
                    IndexPosts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
            [
                'type' => 'post-detail',
                'label' => __('sn-cms::cms.content_type.post_detail'),
                'forms' => fn ($fields) => [
                    Select::make('id')->label(__('sn-cms::cms.post_form.select_post'))
                        ->options(PostModel::published()->scopeable(Utils::getScopeType(), Utils::getScopeId())->limit(30)->pluck('title', 'id'))
                        ->getSearchResultsUsing(fn (string $search): array => PostModel::published()->scopeable(Utils::getScopeType(), Utils::getScopeId())->where('title', 'like', "%{$search}%")->limit(30)->pluck('title', 'id')->toArray())
                        ->placeholder(__('sn-cms::cms.post_form.select_post_placeholder'))
                        ->searchable()
                        ->preload()
                        ->required(),
                ],
                'components' => [
                    Post::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
                // 上下文提供者：选定的文章并入行上下文袋，供同行后续组件（如相关推荐）消费
                'provides' => fn (array $extras) => filled($extras['id'] ?? null) ? ['post' => PostModel::find($extras['id'])] : [],
            ],
            [
                'type' => 'related-posts',
                'label' => __('sn-cms::cms.content_type.related_posts'),
                'forms' => fn ($fields) => [
                    SelectTree::make('categoryIds')->label(__('sn-cms::cms.post_form.categories'))
                        ->query(query: function () {
                            return CategoryModel::scopeable(Utils::getScopeType(), Utils::getScopeId());
                        }, titleAttribute: 'name', parentAttribute: 'parent_id')
                        ->searchable()
                        ->multiple()
                        ->enableBranchNode()
                        ->withCount()
                        ->placeholder(__('sn-cms::cms.post_form.categories_placeholder'))
                        ->emptyLabel(__('sn-cms::cms.post_form.categories_empty'))
                        ->treeKey('relatedPostCategories')
                        ->helperText(__('sn-cms::cms.content_type.related_posts_category_helper')),
                    Select::make('limit')->label(__('sn-cms::cms.content_type.related_posts_limit'))
                        ->options(array_combine(range(3, 12), range(3, 12)))
                        ->default(6),
                ],
                // 上下文消费者：未显式配置分类时，自动注入同行文章组件提供的 post
                'context' => ['post'],
                'components' => [
                    RelatedPosts::class => [
                        'scopeType' => Utils::getScopeType(),
                        'scopeId' => Utils::getScopeId(),
                    ],
                ],
            ],
        ]);
    }
}
