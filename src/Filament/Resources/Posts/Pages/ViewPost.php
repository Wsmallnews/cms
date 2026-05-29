<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Filament\Pages\Comment\Widgets\Comment as CommentWidget;
use Wsmallnews\Preference\Filament\Pages\Preference\Widgets\Views as ViewsWidget;
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

        if (Utils::commentConfig('post', 'enable', false)) {
            $widgets[] = CommentWidget::make([
                'properties' => method_exists(static::getResource(), 'getProperties') ? static::getResource()::getProperties() : [],
                'widgetType' => 'commentable',
                'scopeType' => static::getScopeType(),
                'scopeId' => static::getScopeId(),
                'canAddComment' => Utils::commentConfig('post', 'can_add_comment', false),
                'contentType' => Utils::commentConfig('post', 'content_type', ContentType::Textarea),
                'commentStatus' => Utils::commentConfig('post', 'comment_status'),
            ]);
        }

        $widgets[] = ViewsWidget::make([
            'properties' => method_exists(static::getResource(), 'getProperties') ? static::getResource()::getProperties() : [],
            'widgetType' => 'preferenceable',
            'scopeType' => static::getScopeType(),
            'scopeId' => static::getScopeId(),
        ]);

        return $widgets;
    }
}
