<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Cms\Support\SiteUtils;
use Wsmallnews\Comment\Filament\Pages\Comment\Widgets\Comment as CommentWidget;
use Wsmallnews\Preference\Filament\Pages\Preference\Widgets\Views as ViewsWidget;
use Wsmallnews\Support\Enums\ContentType;
use Wsmallnews\Support\Features\Site\SiteContext;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\ForwardsResourceScope;
use Wsmallnews\Support\Filament\Resources\ScheduledTasks\Widgets\ScheduledTasks as ScheduledTasksWidget;

class ViewPost extends ViewRecord
{
    use ForwardsResourceScope;

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
        $site = static::getSite();

        if (! $site) {
            return [];
        }

        return SiteContext::run($site, function () use ($site): array {
            $widgets = [];

            if (SiteUtils::getCommentConfig('post', 'enable', false)) {
                $widgets[] = CommentWidget::make([
                    'properties' => method_exists(static::getResource(), 'getProperties') ? static::getResource()::getProperties() : [],
                    'widgetType' => 'commentable',
                    'siteId' => $site->id(),
                    'scopeType' => static::getScopeType(),
                    'scopeId' => static::getScopeId(),
                    'canAddComment' => SiteUtils::getCommentConfig('post', 'can_add_comment', false),
                    'contentType' => SiteUtils::getCommentConfig('post', 'content_type', ContentType::Textarea),
                    'commentStatus' => SiteUtils::getCommentConfig('post', 'comment_status'),
                ]);
            }

            $widgets[] = ViewsWidget::make([
                'properties' => method_exists(static::getResource(), 'getProperties') ? static::getResource()::getProperties() : [],
                'widgetType' => 'preferenceable',
                'siteId' => $site->id(),
                'scopeType' => static::getScopeType(),
                'scopeId' => static::getScopeId(),
            ]);

            $widgets[] = ScheduledTasksWidget::make();

            return $widgets;
        });
    }
}
