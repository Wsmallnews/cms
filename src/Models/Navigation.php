<?php

namespace Wsmallnews\Cms\Models;

use Wsmallnews\Cms\Enums\NavigationStatus as NavigationStatusEnum;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
// use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
// use RalphJSmit\Laravel\SEO\SchemaCollection;
// use RalphJSmit\Laravel\SEO\Support\HasSEO;
// use RalphJSmit\Laravel\SEO\Support\SEOData;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\NestedSet;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Navigation extends Model implements HasMedia
{
    use NodeTrait;
    use InteractsWithMedia;
    // use HasSEO;
    use LogsActivity;

    protected $table = 'navigations';

    protected $casts = [
        'type' => NavigationTypeEnum::class,
        'status' => NavigationStatusEnum::class,
        'options' => 'array',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['order_column', 'updated_at'])        // 如果只更新排序，则忽略不记录日志
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }


    public function getScopeAttributes(): array
    {
        return ['team_id'];
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }


    // public function getDynamicSEOData(): SEOData
    // {
    //     return new SEOData(
    //         title: $this->name,
    //         description: $this->description,
    //         image: $this->getFirstMediaUrl('banner')
    //     );
    // }


    public function resolveNavigation($navigation)
    {
        $url = null;

        if ($navigation->type == NavigationTypeEnum::Route && isset($navigation->options['route'])) {
            $params = [];       // 路由参数与 query 合并为一个数组，route 方法会自动区分路由参数，其他的参数 跟在地址栏后面
            $hasRoutes = $navigation->options['_url_params']['has_routes'] ?? false;
            $hasQueries = $navigation->options['_url_params']['has_queries'] ?? false;

            $params = $hasRoutes ? array_merge($params, $navigation->options['_url_params']['routes'] ?? []) : [];
            $params = $hasQueries ? array_merge($params, $navigation->options['_url_params']['queries'] ?? []) : [];

            $url = sn_route($navigation->options['route'], $params);
        }

        if ($navigation->type == NavigationTypeEnum::Page) {
            $url = sn_route('navigation', $navigation->slug);
        }

        if ($navigation->type == NavigationTypeEnum::Url && isset($navigation->options['url'])) {
            $url = $navigation->options['url'];
        }

        if ($navigation->type == NavigationTypeEnum::Content) {
            $url = sn_route('navigation', $navigation->slug);
        }

        $navigation->setAttribute('url_info', [
            'url' => $url,
            'target' => isset($navigation->options['target']) && $navigation->options['target'] == '_blank' ? true : false,
        ]);

        return $navigation;
    }


    public function scopeNormal($query)
    {
        return $query->where('status', NavigationStatus::Normal);
    }


    public function scopeHidden($query)
    {
        return $query->where('status', NavigationStatus::Hidden);
    }


    public function content(): MorphOne
    {
        return $this->morphOne(Content::class, 'contentable');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
