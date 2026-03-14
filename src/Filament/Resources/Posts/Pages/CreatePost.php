<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Support\Filament\Resources\Concerns\Pages\Scopeable;

class CreatePost extends CreateRecord
{
    use Scopeable;

    protected static string $resource = PostResource::class;

    /**
     * Mutate the form data before creating a record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 合并 scopeinfo 参数
        $data = array_merge($data, static::getScopeable());

        // 合并 publisher 参数
        $admin = Filament::auth()->user();
        $data = array_merge($data, [
            'publisher_type' => $admin->getMorphClass(),
            'publisher_id' => $admin->id,
        ]);

        if ($data['status'] === PostStatus::Published) {
            $data['published_at'] = now();
        }

        return parent::mutateFormDataBeforeCreate($data);
    }
}
