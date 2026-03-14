<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Wsmallnews\Category\Support\Utils as CategoryUtils;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Models\SupportModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class Post extends SupportModel implements HasMedia
{
    use HasTags;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'sn_posts';

    protected $casts = [
        'options' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'status' => PostStatus::class,
    ];

    /**
     * 获取 tag model
     */
    public static function getTagClassName(): string
    {
        return Utils::getTagModel();
    }

    /**
     * post 分类多对多查询
     */
    public function scopeCategoryIds($query, array | Collection $categoryIds)
    {
        return $query->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('id', $categoryIds);
        });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', PostStatus::Draft);
    }

    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::Published);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', PostStatus::Hidden);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', PostStatus::Scheduled);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CategoryUtils::getCategoryModel(), 'sn_category_post');
    }

    public function content(): MorphOne
    {
        return $this->morphOne(SupportUtils::getContentModel(), 'contentable');
    }

    public function publisher(): MorphTo
    {
        return $this->morphTo();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getTenantModel());
    }
}
