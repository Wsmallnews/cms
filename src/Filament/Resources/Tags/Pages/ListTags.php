<?php

namespace Wsmallnews\Cms\Filament\Resources\Tags\Pages;

use Wsmallnews\Cms\Filament\Resources\Tags\TagResource;
use Wsmallnews\Support\Filament\Resources\Tags\Pages\ListTags as BaseListTags;

class ListTags extends BaseListTags
{
    protected static string $resource = TagResource::class;
}
