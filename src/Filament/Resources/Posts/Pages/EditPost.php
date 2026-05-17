<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Comment\Filament\Pages\Comment\Widgets\Commentable as CommentableWidgets;
use Wsmallnews\Support\Enums\ContentType;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class EditPost extends EditRecord
{
    use Scopeable;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        $record = $this->getRecord();

        return [
            CommentableWidgets::make([
                // 'properties' => static::getResource()::getProperties() ?? [],
                'scope_type' => static::getScopeType(),
                'scope_id' => static::getScopeId(),
                // 'content_type' => Utils::canComment('content_type'),
                'content_type' => ContentType::Textarea,
                'key' => 'widgets-post:' . $record?->id . '-comment',
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 编辑时候，默认设置为 scheduled_at 类型
        $data['scheduled_at_type'] = 'scheduled_at';

        return $data;
    }

    /**
     * Mutate the form data before creating a record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        if ($data['status'] === PostStatus::Published && blank($record->published_at)) {
            $data['published_at'] = now();
        }

        if ($data['status'] === PostStatus::Scheduled) {
            if ($data['scheduled_at_type'] === 'minutes_later') {
                $data['scheduled_at'] = now()->addMinutes($data['minutes_later']);
            } else {
                $data['scheduled_at'] = $data['scheduled_at'];
            }
        }

        // 移除 scheduled_at_type 和 minutes_later 参数
        unset($data['scheduled_at_type'], $data['minutes_later']);

        return parent::mutateFormDataBeforeSave($data);
    }
}
