<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wsmallnews\Cms\Enums\NavigationStatus as NavigationStatusEnum;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Models\SupportModel;

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

    public function resolveNavigation($navigation)
    {
        $url = null;

        if ($navigation->type == NavigationTypeEnum::Route && isset($navigation->options['route'])) {
            $params = [];       // 路由参数与 query 合并为一个数组，route 方法会自动区分路由参数，其他的参数 跟在地址栏后面
            $hasRoutes = $navigation->options['_url_params']['has_routes'] ?? false;
            $hasQueries = $navigation->options['_url_params']['has_queries'] ?? false;

            $params = $hasRoutes ? array_merge($params, $navigation->options['_url_params']['routes'] ?? []) : [];
            $params = $hasQueries ? array_merge($params, $navigation->options['_url_params']['queries'] ?? []) : $params;

            $url = sn_route($navigation->options['route'], $params);
        }

        if ($navigation->type == NavigationTypeEnum::Page) {
            $url = sn_route('cms.navigation', $navigation->slug);
        }

        if ($navigation->type == NavigationTypeEnum::Url && isset($navigation->options['url'])) {
            $url = $navigation->options['url'];
        }

        if ($navigation->type == NavigationTypeEnum::Content) {
            $url = sn_route('cms.navigation', $navigation->slug);
        }

        $navigation->setAttribute('url_info', [
            'url' => $url,
            'target' => isset($navigation->options['target']) && $navigation->options['target'] == '_blank' ? true : false,
        ]);

        return $navigation;
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
        return $this->morphOne(Content::class, 'contentable');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Utils::getTenantModel());
    }
}
