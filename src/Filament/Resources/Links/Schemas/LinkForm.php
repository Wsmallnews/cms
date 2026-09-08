<?php

namespace Wsmallnews\Cms\Filament\Resources\Links\Schemas;

use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Wsmallnews\Cms\Enums\LinkStatus;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Forms\FormComponents;

class LinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Schemas\Components\Section::make(__('sn-cms::cms.link_form.basic_info'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('sn-cms::cms.link_form.name'))
                    ->placeholder(__('sn-cms::cms.link_form.name_placeholder'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->label(__('sn-cms::cms.link_form.url'))
                    ->placeholder(__('sn-cms::cms.link_form.url_placeholder'))
                    ->url()
                    ->required()
                    ->maxLength(255),
                FormComponents::plainImageUpload('logo')
                    ->label(__('sn-cms::cms.link_form.logo'))
                    ->directory(Utils::getFileDirectory('links'))
                    ->uploadingMessage(__('sn-cms::cms.link_form.logo_uploading')),
                Forms\Components\TextInput::make('group_name')
                    ->label(__('sn-cms::cms.link_form.group_name'))
                    ->placeholder(__('sn-cms::cms.link_form.group_name_placeholder'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('order_column')
                    ->label(__('sn-cms::cms.link_form.order'))
                    ->integer()
                    ->placeholder(__('sn-cms::cms.link_form.order_placeholder'))
                    ->rules(['integer', 'min:0']),
                Forms\Components\Toggle::make('nofollow')
                    ->label(__('sn-cms::cms.link_form.nofollow'))
                    ->helperText(__('sn-cms::cms.link_form.nofollow_helper'))
                    ->default(false)
                    ->inline(false),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('sn-cms::cms.link_form.status'))
                    ->default(LinkStatus::Normal)
                    ->inline()
                    ->grouped()
                    ->options(LinkStatus::class),
            ])
            ->columns(2)
            ->columnSpanFull(),
        ]);
    }
}
