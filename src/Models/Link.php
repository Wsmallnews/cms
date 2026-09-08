<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Wsmallnews\Cms\Enums\LinkStatus;
use Wsmallnews\Support\Contracts\HasSnSubject;
use Wsmallnews\Support\Models\Concerns\HasActivityLog;
use Wsmallnews\Support\Models\SupportModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class Link extends SupportModel implements HasSnSubject
{
    use HasActivityLog;

    protected $table = 'sn_links';

    protected $casts = [
        'nofollow' => 'boolean',
        'status' => LinkStatus::class,
    ];

    /**
     * 搜索字段（用于 morphFilter 关键词搜索）。
     */
    public static array $keywordSearchFields = ['name', 'url'];

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
        return null;
    }

    public function scopeNormal($query)
    {
        return $query->where('status', LinkStatus::Normal);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', LinkStatus::Hidden);
    }

    /**
     * 友情链接排序（order_column 大者在前，与图文排序语义一致）
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('order_column')->orderByDesc('id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getTenantModel());
    }
}
