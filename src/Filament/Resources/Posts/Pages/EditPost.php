<?php

namespace Wsmallnews\Cms\Filament\Resources\Posts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Wsmallnews\Cms\Enums\PostStatus;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
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

    /**
     * Mutate the form data before creating a record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] === PostStatus::Published && ! filled($data['published_at'])) {
            $data['published_at'] = now();
        }

        return parent::mutateFormDataBeforeCreate($data);
    }

}
