<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wsmallnews\Cms\Enums\NavigationTypeStatus;
use Wsmallnews\Support\Models\SupportModel;

class NavigationType extends SupportModel
{
    use SoftDeletes;

    protected $table = 'sn_navigation_types';

    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'status' => NavigationTypeStatus::class,
    ];

    public function scopeNormal($query)
    {
        return $query->where('status', 'normal');
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', 'disabled');
    }

    public function navigations(): HasMany
    {
        return $this->hasMany(Navigation::class, 'type_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
