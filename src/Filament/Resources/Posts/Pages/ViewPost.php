<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Filament\Pages\Comment\Widgets\Comment as CommentWidgets;
use Wsmallnews\Support\Enums\ContentType;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class ViewPost extends ViewRecord
{
    use Scopeable;

    protected static string $resource = PostResource::class;

    protected string $view = 'sn-cms::filament.resources.posts.pages.view-post';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        $record = $this->getRecord();

        $widgets = [];

        if (Utils::commentConfig('post', 'can_comment', false)) {
            $widgets[] = CommentWidgets::make([
                'properties' => method_exists(static::getResource(), 'getProperties') ? static::getResource()::getProperties() : [],
                'widget_type' => 'commentable',
                'scope_type' => static::getScopeType(),
                'scope_id' => static::getScopeId(),
                'content_type' => Utils::commentConfig('post', 'content_type', ContentType::Textarea)
            ]);
        }

        return $widgets;
    }
}
