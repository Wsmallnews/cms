<?php

namespace Wsmallnews\Cms\Models;

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
use Wsmallnews\Support\Models\SupportModel;

use function Filament\Support\generate_icon_html;

class Navigation extends SupportModel implements HasMedia
{
    use InteractsWithMedia;
    use NodeTrait;

    protected $table = 'sn_navigations';

    protected $casts = [
        'type' => NavigationTypeEnum::class,
        'options' => 'array',
        'status' => NavigationStatusEnum::class,
    ];

    public function getScopeAttributes(): array
    {
        $scopes = ['scope_type', 'scope_id', 'type_id'];
        if (Utils::isTenancyEnabled()) {        // 多租户 时，自动增加 租户相关参数
            $scopes[] = 'team_id';
        }

        return $scopes;
    }

    public function getRouteKeyName()
    {
        return 'slug';
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

                    $url = sn_route($options['route'], $params);
                }

                if ($this->type == NavigationTypeEnum::Page) {
                    $url = sn_route('cms.navigation', $attributes['slug']);
                }

                if ($this->type == NavigationTypeEnum::Url && isset($options['url'])) {
                    $url = $options['url'];
                }

                if ($this->type == NavigationTypeEnum::Content) {
                    $url = sn_route('cms.navigation', $attributes['slug']);
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
     *
     * @return Attribute
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $urlInfo = $this->url_info;
                $fullUrl = request()->fullUrl();

                return $urlInfo['url'] == $fullUrl;
            }
        );
    }

    /**
     * 导航名称（包含 icon）
     *
     * @return Attribute
     */
    protected function nameLabel(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // 当前的导航活动，（或者已经加载过子导航的页面，子导航中是否有活动，未加载则直接 false）
                $isActive = $this->is_active || ($this->relationLoaded('children') ? $this->children->contains('is_active', true) : false);
                $recordLabel = '<span class="flex items-center gap-2">';
                $icon_type = $this->options['icon_type'] ?? 'none';
                if ($icon_type == 'icon') {
                    if ($isActive) {
                        $icon = $this->options['active_icon'] ?? ($this->options['icon'] ?? '');        // 优先取 活动图标
                    } else {
                        $icon = $this->options['icon'] ?? ($this->options['active_icon'] ?? '');        // 优先取非活动图标
                    }
                    $icon && $recordLabel .= generate_icon_html($icon, size: IconSize::Large)->toHtml();
                } elseif ($icon_type == 'image') {
                    if ($isActive) {
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
        return $this->morphOne(Utils::getContentModel(), 'contentable');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Utils::getTenantModel());
    }
}
