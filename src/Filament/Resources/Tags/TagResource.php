<?php

namespace Wsmallnews\Cms\Filament\Resources\Tags;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Filament\Resources\Tags\Pages\CreateTag;
use Wsmallnews\Cms\Filament\Resources\Tags\Pages\EditTag;
use Wsmallnews\Cms\Filament\Resources\Tags\Pages\ListTags;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Resources\ResourceConfiguration;
use Wsmallnews\Support\Filament\Resources\Tags\BaseResource as BaseTagResource;

final class TagResource extends BaseTagResource
{
    use CanBeConfigured;

    protected static ?string $configurationClass = ResourceConfiguration::class;

    public static function getModel(): string
    {
        return Utils::getTagModel();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }

    public static function getTagType(): string
    {
        return static::resolveCustomProperty('tag_type') ?? parent::getTagType();
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
