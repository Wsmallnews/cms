<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Wsmallnews\Category\Support\Utils as CategoryUtils;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Models\Concerns\Commentable;
use Wsmallnews\Preference\Models\Concerns\Preferenceable;
use Wsmallnews\Preference\Models\Concerns\Preferenceable\Viewable;
use Wsmallnews\Support\Casts\CounterCast;
use Wsmallnews\Support\Contracts\HasSnSubject;
use Wsmallnews\Support\Models\Concerns\HasActivityLog;
use Wsmallnews\Support\Models\SupportModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class Post extends SupportModel implements HasMedia, HasSnSubject
{
    use Commentable;
    use HasActivityLog;
    use HasTags;
    use InteractsWithMedia;
    use Preferenceable;
    use SoftDeletes;
    use Viewable;

    protected $table = 'sn_posts';

    protected $casts = [
        'counter' => CounterCast::class,
        'published_at' => 'datetime',
        'flags' => 'array',
        'options' => 'array',
        'status' => PostStatus::class,
    ];

    /**
     * 搜索字段（用于 morphFilter 关键词搜索）。
     */
    public static array $keywordSearchFields = ['title', 'description'];

    protected function getActivityTitleAttribute(): string
    {
        return 'title';
    }

    public function getRouteKeyName()
    {
        return is_in_panel() ? $this->getKeyName() : Utils::getConfig('routes.route_key_name.post', 'slug');
    }

    /**
     * 获取 tag model
     */
    public static function getTagClassName(): string
    {
        return Utils::getTagModel();
    }

    public function getSnSubjectId(): int
    {
        return $this->id;
    }

    public function getSnSubjectTitle(): string | HtmlString | null
    {
        return $this->title;
    }

    public function getSnSubjectDescription(): string | HtmlString | null
    {
        return $this->description;
    }

    public function getSnSubjectCoverUrl(): string | HtmlString | null
    {
        return $this->getFirstMediaUrl('post_image');
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

    /**
     * 范围查询：有指定标签的帖子
     *
     * @return Builder
     */
    public function scopeHasFlag(Builder $query, mixed $flag)
    {
        return $query->whereJsonContains('flags', $flag);
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

    /**
     * 定时调度任务关联
     */
    public function scheduledTasks(): MorphMany
    {
        return $this->morphMany(SupportUtils::getScheduledTaskModel(), 'schedulable');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getTenantModel());
    }
}
