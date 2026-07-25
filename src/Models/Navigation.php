<?php

namespace Wsmallnews\Cms\Models;

use Filament\Facades\Filament;
use Filament\Support\Enums\IconSize;
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

    public function getRouteKeyName()
    {
        return Utils::getConfig('routes.route_key_name.navigation', 'slug');
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

    public function getSnSubjectHrefUrl(): string | HtmlString | null
    {
        return null;
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
                    // cms 导航页面，使用 Utils 路由方法拼接 cms 路由前缀
                    $url = Utils::route('navigation.show', $this);
                }

                if ($this->type == NavigationTypeEnum::Url && isset($options['url'])) {
                    $url = $options['url'];
                }

                if ($this->type == NavigationTypeEnum::Content) {
                    // cms 内容页面，使用 Utils 路由方法拼接 cms 路由前缀
                    $url = Utils::route('navigation.show', $this);
                }

                return [
                    'url' => $url,
                    'target' => isset($options['target']) && $options['target'] == '_blank' ? true : false,
                ];
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

                $recordLabel .= $attributes['name'] . '</span>';

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

    public function content(): MorphOne
    {
        return $this->morphOne(SupportUtils::getContentModel(), 'contentable');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getTenantModel());
    }
}
