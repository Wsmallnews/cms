<?php

namespace Wsmallnews\Cms\Models;

use Filament\Facades\Filament;
use Filament\Support\Enums\IconSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\HtmlString;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wsmallnews\Cms\Enums\NavigationStatus as NavigationStatusEnum;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Contracts\HasSnSubject;
use Wsmallnews\Support\Models\Concerns\HasActivityLog;
use Wsmallnews\Support\Models\SupportModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

use function Filament\Support\generate_icon_html;

class Navigation extends SupportModel implements HasMedia, HasSnSubject
{
    use HasActivityLog;
    use InteractsWithMedia;
    use NodeTrait;

    protected $table = 'sn_navigations';

    protected $casts = [
        'type' => NavigationTypeEnum::class,
        'options' => 'array',
        'status' => NavigationStatusEnum::class,
    ];

    /**
     * 搜索字段（用于 morphFilter 关键词搜索）。
     */
    public static array $keywordSearchFields = ['name', 'description'];

    protected function getActivityIgnoreAttributes(): array
    {
        return ['_lft', '_rgt', 'updated_at'];
    }

    public function getScopeAttributes(): array
    {
        $scopes = ['scope_type', 'scope_id', 'type_id'];
        if (SupportUtils::isTenancyEnabled()) {        // 多租户 时，自动增加 租户相关参数
            $scopes[] = 'team_id';
        }

        return $scopes;
    }

    protected static function booted(): void
    {
        static::saving(function (Navigation $navigation) {
            if (! ($navigation->options['is_home'] ?? false)) {
                return;
            }

            // 首页节点强制为页面类型（表单勾选设为首页时自动切换，这里兜底保证数据一致）；
            // 首页内容经 page_id → Page 承载
            $navigation->type = NavigationTypeEnum::Page;

            // 首页标记互斥：退位节点清掉标记（is_home 已置 false，saving 钩子早退，不会递归）
            static::query()
                ->where('options->is_home', true)
                ->snScope($navigation->scope_type, $navigation->scope_id)
                ->when($navigation->exists, fn (Builder $query) => $query->whereKeyNot($navigation->getKey()))
                ->get()
                ->each(function (Navigation $oldHome) {
                    $oldHome->options = [...($oldHome->options ?? []), 'is_home' => false];
                    $oldHome->save();
                });
        });
    }

    public function getSnSubjectId(): int
    {
        return $this->id;
    }

    public function getSnSubjectTitle(): string | HtmlString | null
    {
        return $this->name;
    }

    public function getSnSubjectDescription(): string | HtmlString | null
    {
        return $this->description;
    }

    public function getSnSubjectCoverUrl(): string | HtmlString | null
    {
        return $this->getFirstMediaUrl('navigation_banner') ?: null;
    }

    protected function urlInfo(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $options = $this->options;
                $url = null;

                if ($this->type == NavigationTypeEnum::Route && isset($options['route'])) {
                    $params = [];       // 路由参数与 query 合并为一个数组，route 方法会自动区分路由参数，其他的参数 跟在地址栏后面
                    $hasRoutes = $options['_url_params']['has_routes'] ?? false;
                    $hasQueries = $options['_url_params']['has_queries'] ?? false;

                    $params = $hasRoutes ? array_merge($params, $options['_url_params']['routes'] ?? []) : [];
                    $params = $hasQueries ? array_merge($params, $options['_url_params']['queries'] ?? []) : $params;

                    // 这里的 route 必须是完整 name
                    $url = sn_route($options['route'], $params);
                }

                if ($this->type == NavigationTypeEnum::Page) {
                    // 页面节点：首页标记指向模块首页（前缀 + /）；否则指向 Page 规范地址；
                    // 未绑定 page_id（配置未完成）输出空链接
                    if ($options['is_home'] ?? false) {
                        $url = Utils::route('index');
                    } elseif ($this->page?->slug) {
                        $url = Utils::route('pages.show', $this->page->slug);
                    } else {
                        $url = '#';
                    }
                }

                if ($this->type == NavigationTypeEnum::Url && isset($options['url'])) {
                    $url = $options['url'];
                }

                return [
                    'url' => $url,
                    'target' => isset($options['target']) && $options['target'] == '_blank' ? true : false,
                ];
            }
        );
    }

    /**
     * 第一个可用叶子的 url（hover 级联下父项的直达目标）
     *
     * 沿已加载的 children 向下找第一个可用（normal）子项，直到叶子；
     * 子项全部不可用时停留在当前节点（回退为自身 url，无链接则为 null）。
     * 整树已 toTree() 加载时递归无 N+1。
     */
    protected function firstLeafUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $node = $this;
                while ($node->children->isNotEmpty()) {
                    $next = $node->children
                        ->filter(fn ($child) => $child->status === NavigationStatusEnum::Normal)
                        ->first();          // 跳过隐藏项，找第一个可用子项

                    if (! $next) {
                        break;
                    }

                    $node = $next;
                }

                return $node->url_info['url'] ?? null;
            }
        );
    }

    /**
     * 当前是否是激活状态
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $panel = Filament::getCurrentPanel();
                if ($panel) {       // 在 panel 面板中时不校验活动状态
                    return false;
                }
                $urlInfo = $this->url_info;
                $fullUrl = request()->fullUrl();

                return $urlInfo['url'] == $fullUrl;
            }
        );
    }

    /**
     * 当前导航以及子导航中是否存在 激活状态
     */
    protected function hasActive(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($this->is_active) {
                    return true;
                }

                // 收集当前 model 所有已经加载的 children
                $allChildren = collect([]);
                if ($this->relationLoaded('children')) {
                    $allChildren = tree_to_flatten($this->children);
                }

                return $allChildren->contains('is_active', true);
            }
        );
    }

    /**
     * 导航名称（包含 icon）
     */
    protected function nameLabel(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // 当前的导航是否活动，或者子导航中是否存在活动状态
                $hasActive = $this->has_active;
                $recordLabel = '<span class="flex items-center gap-2">';
                $icon_type = $this->options['icon_type'] ?? 'none';
                if ($icon_type == 'icon') {
                    if ($hasActive) {
                        $icon = $this->options['active_icon'] ?? ($this->options['icon'] ?? '');        // 优先取 活动图标
                    } else {
                        $icon = $this->options['icon'] ?? ($this->options['active_icon'] ?? '');        // 优先取非活动图标
                    }
                    $icon && $recordLabel .= generate_icon_html($icon, size: IconSize::Large)->toHtml();
                } elseif ($icon_type == 'image') {
                    if ($hasActive) {
                        $image = $this->options['active_icon_src'] ?? ($this->options['icon_src'] ?? '');    // 优先取 活动图标
                    } else {
                        $image = $this->options['icon_src'] ?? ($this->options['active_icon_src'] ?? '');   // 优先取非活动图标
                    }
                    $image && $recordLabel .= '<img src="' . files_url($image) . '" class="size-6" />';
                }

                $recordLabel .= $attributes['name'];

                // 后台树列表标记首页节点（前台导航不展示；name_label 前后台共用，按面板语境门控）
                if (is_in_panel() && ($this->options['is_home'] ?? false)) {
                    $recordLabel .= '<span class="ml-1 inline-flex items-center rounded-md bg-primary-100 px-1.5 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">' . __('sn-cms::cms.navigation_form.home_badge') . '</span>';
                }

                $recordLabel .= '</span>';

                return new HtmlString($recordLabel);
            },
        );
    }

    public function scopeNormal($query)
    {
        return $query->where('status', NavigationStatusEnum::Normal);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', NavigationStatusEnum::Hidden);
    }

    /**
     * 页面节点引用的 Page 实体（urlInfo / 前台渲染按需预加载）
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getPageModel());
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getTenantModel());
    }
}
