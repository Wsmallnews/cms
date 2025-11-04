<?php

namespace Wsmallnews\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Models\SupportModel;

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
        return $this->belongsTo(Utils::getTenantModel());
    }
}
