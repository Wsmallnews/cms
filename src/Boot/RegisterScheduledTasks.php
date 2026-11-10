<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Support\Facades\ScheduledTask;
use Wsmallnews\Support\Features\Site\Site;

/**
 * Scheduled actions bound to a model (module level, not per site).
 */
final class RegisterScheduledTasks
{
    public function register(): void
    {
        // 注册 Post 的定时调度动作（publish / unpublish）
        ScheduledTask::registers('sn_post', [
            [
                'action' => 'publish',
                'label' => __('sn-cms::cms.post_form.schedule_publish'),
                'forms' => fn () => [],
                'handler' => fn ($task, ?array $payload): bool => $task->schedulable->update([
                    'status' => PostStatus::Published,
                    'published_at' => $task->schedulable->published_at ?? now(),
                ]),
            ],
            [
                'action' => 'unpublish',
                'label' => __('sn-cms::cms.post_form.schedule_unpublish'),
                'forms' => fn () => [],
                'handler' => fn ($task, ?array $payload): bool => $task->schedulable->update([
                    'status' => PostStatus::Hidden,
                ]),
            ],
        ]);
    }
}
