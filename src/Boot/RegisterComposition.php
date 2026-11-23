<?php

namespace Wsmallnews\Cms\Boot;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\Builder;
use Wsmallnews\Category\Models\Category as CategoryModel;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Livewire\Components\Post\IndexPosts;
use Wsmallnews\Cms\Livewire\Components\Post\Post;
use Wsmallnews\Cms\Livewire\Components\Post\Posts;
use Wsmallnews\Cms\Livewire\Components\Post\RelatedPosts;
use Wsmallnews\Cms\Models\Post as PostModel;
use Wsmallnews\Support\Exceptions\InvalidScopeException;
use Wsmallnews\Support\Facades\CompositionRegistry as CompositionRegistryFacade;
use Wsmallnews\Support\Features\Composition\CompositionRenderer;
use Wsmallnews\Support\Features\Site\Site;

/**
 * Composable content types the module exposes to the composition editor.
 *
 * Each type is one use of a public component, registered with its partition
 * suffixes fixed at registration: the operator picks a labelled type and never
 * types a suffix. The renderer hands those suffixes to the component and to the
 * type's own form/provider, which resolve them against the site rendering the
 * composition — so a type can never reach outside that site's base scope.
 *
 * A component with several uses is registered once per use: the `*Type()`
 * factories return a complete definition for a given set of suffixes, so a
 * host (or the module itself) registers another use with a new type key, a
 * telling label and its partitions, reusing the same form and provider.
 *
 * Standard signatures — every factory takes `(?string $scopeSuffix, array
 * $relatedScopeSuffix)`, every `forms` closure receives
 * `($fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix)`
 * and every `provides` closure `($extras, ?Site $site, ?string $scopeSuffix,
 * array $relatedScopeSuffix)`. The renderer injects the suffixes from the type
 * definition, so the closures never capture them.
 */
final class RegisterComposition
{
    public function register(): void
    {
        // key = 模块标识插件 id，与站点 scope 无关——同一套类型服务所有站点，分区在渲染期按站点解析
        CompositionRegistryFacade::registers(app(CmsPlugin::class)->getId(), [
            static::postsType('posts', __('sn-cms::cms.content_type.posts')),
            static::indexPostsType('index-posts', __('sn-cms::cms.content_type.index_posts')),
            static::postDetailType('post-detail', __('sn-cms::cms.content_type.post_detail')),
            static::relatedPostsType('related-posts', __('sn-cms::cms.content_type.related_posts')),
        ]);
    }

    /**
     * 文章列表。`$scopeSuffix` 限定文章所在分区，`$relatedScopeSuffix` 声明各关联资源的分区
     * （分类树读 `categories` 键，未声明 = 站点 base）。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     * @return array<string, mixed>
     */
    public static function postsType(string $type, string $label, ?string $scopeSuffix = null, array $relatedScopeSuffix = []): array
    {
        return [
            'type' => $type,
            'label' => $label,
            'scopeSuffix' => $scopeSuffix,
            'relatedScopeSuffix' => $relatedScopeSuffix,
            // 字段平铺：外层 extras Fieldset 已统一单列（编排槽位有宽窄，编排表单不要再嵌自带列数的布局组件）
            'forms' => fn (array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix) => static::postsForm($fields, $site, $scopeSuffix, $relatedScopeSuffix),
            'components' => [
                // 链接由调用处给：公共组件不猜站点路由名
                Posts::class => [
                    'customRoutes' => ['detail' => 'posts.show'],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, string|null>  $relatedScopeSuffix
     * @return array<string, mixed>
     */
    public static function indexPostsType(string $type, string $label, ?string $scopeSuffix = null, array $relatedScopeSuffix = []): array
    {
        return [
            'type' => $type,
            'label' => $label,
            'scopeSuffix' => $scopeSuffix,
            'relatedScopeSuffix' => $relatedScopeSuffix,
            'forms' => fn (array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix) => [],
            'components' => [
                IndexPosts::class => [
                    'customRoutes' => ['detail' => 'posts.show'],
                ],
            ],
        ];
    }

    /**
     * 文章详情，同时是上下文提供者：选定的文章并入行上下文袋，供同行后续组件
     * （如相关推荐）消费。`$scopeSuffix` 限定文章所在分区（null = 站点 base）。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     * @return array<string, mixed>
     */
    public static function postDetailType(string $type, string $label, ?string $scopeSuffix = null, array $relatedScopeSuffix = []): array
    {
        return [
            'type' => $type,
            'label' => $label,
            'scopeSuffix' => $scopeSuffix,
            'relatedScopeSuffix' => $relatedScopeSuffix,
            'forms' => fn (array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix) => static::postDetailForm($fields, $site, $scopeSuffix, $relatedScopeSuffix),
            'components' => [
                Post::class,
            ],
            // 只接受本类型分区内的文章：手工 POST / 导入的跨 scope id 不注入
            'provides' => static::postDetailProvides(),
        ];
    }

    /**
     * 相关文章推荐，是上下文消费者：未显式配置分类时自动注入同行文章组件提供的 post。
     * `$scopeSuffix` 限定推荐文章所在分区，`$relatedScopeSuffix` 声明分类树分区（`categories` 键）。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     * @return array<string, mixed>
     */
    public static function relatedPostsType(string $type, string $label, ?string $scopeSuffix = null, array $relatedScopeSuffix = []): array
    {
        return [
            'type' => $type,
            'label' => $label,
            'scopeSuffix' => $scopeSuffix,
            'relatedScopeSuffix' => $relatedScopeSuffix,
            'forms' => fn (array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix) => static::relatedPostsForm($fields, $site, $scopeSuffix, $relatedScopeSuffix),
            'consumes' => ['post'],
            'components' => [
                RelatedPosts::class => [
                    'customRoutes' => ['detail' => 'posts.show'],
                ],
            ],
        ];
    }

    /**
     * 文章列表表单：分类选项取自本类型声明的 categories 分区（未声明 = 站点 base）。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     */
    private static function postsForm(array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix): array
    {
        return [
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
                ->query(query: fn () => static::categoryTreeQuery($site, $relatedScopeSuffix), titleAttribute: 'name', parentAttribute: 'parent_id')
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
        ];
    }

    /**
     * 文章详情表单：可选文章限定在本类型自身的数据空间内。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     */
    private static function postDetailForm(array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix): array
    {
        // 必选字段不能静默消失（返回 [] 会让必填校验失效、存下无文章的详情块）：
        // 无站点直接抛，后台编辑一定有站点
        $scopeable = CompositionRenderer::scopeOfSuffix(
            $site ?? throw InvalidScopeException::missingScopeable('the post-detail form has no site to resolve its scope against'),
            $scopeSuffix,
        );

        return [
            Select::make('id')->label(__('sn-cms::cms.post_form.select_post'))
                ->options(PostModel::published()->forScopeable($scopeable)->limit(30)->pluck('title', 'id'))
                // A post may leave the published set after being bound: keep its label readable.
                ->getOptionLabelUsing(fn ($value): ?string => PostModel::query()->withTrashed()->whereKey($value)->value('title'))
                ->getSearchResultsUsing(fn (string $search): array => PostModel::published()->forScopeable($scopeable)->where('title', 'like', "%{$search}%")->limit(30)->pluck('title', 'id')->toArray())
                ->placeholder(__('sn-cms::cms.post_form.select_post_placeholder'))
                ->searchable()
                ->preload()
                ->required(),
        ];
    }

    /**
     * 上下文提供者：在本类型自身的数据空间里查文章，而不是编排记录所在的分区——
     * 分区详情组件即使被放进 base 编排，查的仍是它声明的那个分区。
     * 站点缺失（无站点上下文）或文章不属于该分区时返回空数组：静默失败，不拖垮整页。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     */
    private static function postDetailProvides(): \Closure
    {
        return function (array $extras, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix): array {
            if (blank($extras['id'] ?? null) || $site === null) {
                return [];
            }

            $post = PostModel::query()
                ->forScopeable(CompositionRenderer::scopeOfSuffix($site, $scopeSuffix))
                ->find($extras['id']);

            return $post ? ['post' => $post] : [];
        };
    }

    /**
     * @param  array<string, string|null>  $relatedScopeSuffix
     */
    private static function relatedPostsForm(array $fields, ?Site $site, ?string $scopeSuffix, array $relatedScopeSuffix): array
    {
        return [
            SelectTree::make('categoryIds')->label(__('sn-cms::cms.post_form.categories'))
                ->query(query: fn () => static::categoryTreeQuery($site, $relatedScopeSuffix), titleAttribute: 'name', parentAttribute: 'parent_id')
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
        ];
    }

    /**
     * 分类树查询：取本类型 relatedScopeSuffix['categories'] 声明的分区（未声明回落站点 base）。
     * 无站点时空集——没有站点就无法解析分区，不读任何猜出来的数据空间。
     *
     * @param  array<string, string|null>  $relatedScopeSuffix
     */
    private static function categoryTreeQuery(?Site $site, array $relatedScopeSuffix): Builder
    {
        if ($site === null) {
            return CategoryModel::query()->whereRaw('1 = 0');
        }

        return CategoryModel::query()->forScopeable(
            CompositionRenderer::scopeOfSuffix($site, $relatedScopeSuffix['categories'] ?? null),
        );
    }
}
