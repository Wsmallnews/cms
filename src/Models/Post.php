<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Wsmallnews\Category\Models\Category;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Models\SupportModel;

class Post extends SupportModel implements HasMedia
{
    use HasTags;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'sn_posts';

    protected $casts = [
        'options' => 'array',
        'status' => PostStatus::class,
    ];

    /**
     * post 分类多对多查询
     */
    public function scopeWhereCategoryIn($query, array | Collection $categoryIds)
    {
        return $query->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('id', $categoryIds);
        });
    }

    public function scopeNormal($query)
    {
        return $query->where('status', PostStatus::Normal);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', PostStatus::Hidden);
    }

    public function content(): MorphOne
    {
        return $this->morphOne(Utils::getContentModel(), 'contentable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'sn_category_post');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Utils::getTenantModel());
    }
}
