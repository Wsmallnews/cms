<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wsmallnews\Support\Models\SupportModel;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class Content extends SupportModel
{
    protected $table = 'sn_contents';

    protected $casts = [];

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SupportUtils::getTenantModel());
    }
}
