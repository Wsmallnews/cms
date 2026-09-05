<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;
use Wsmallnews\Support\Filament\Forms\FormComponents;
use Wsmallnews\Support\Filament\Pages\PageConfiguration;

class GeneralSetting extends SettingsPage
{
    use CanBeConfigured;

    protected static ?string $configurationClass = PageConfiguration::class;

    protected static string $settings = GeneralSettings::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        // 覆盖 CanBeConfigured 中的 getNavigationLabel
        return static::getConfigurationValue('navigationLabel') ?? __('sn-cms::cms.general_setting_page.navigation_label');
    }

    public function getTitle(): string
    {
        return static::getNavigationLabel() ?? __('sn-cms::cms.general_setting.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make(__('sn-cms::cms.general_setting.site_section'))->schema([
                    Forms\Components\TextInput::make('site_name')
                        ->label(__('sn-cms::cms.general_setting.site_name'))
                        ->helperText(__('sn-cms::cms.general_setting.site_name_helper'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('site_slogan')
                        ->label(__('sn-cms::cms.general_setting.site_slogan'))
                        ->helperText(__('sn-cms::cms.general_setting.site_slogan_helper'))
                        ->maxLength(255),
                    FormComponents::plainImageUpload('logo')
                        ->label(__('sn-cms::cms.general_setting.logo'))
                        ->directory(Utils::getFileDirectory('settings'))
                        ->uploadingMessage(__('sn-cms::cms.general_setting.logo_uploading')),
                    Forms\Components\ToggleButtons::make('logo_with_site_name')
                        ->label(__('sn-cms::cms.general_setting.logo_with_site_name'))
                        ->helperText(__('sn-cms::cms.general_setting.logo_with_site_name_helper'))
                        ->boolean()
                        ->grouped()
                        ->default(true)
                        ->inline(),
                    FormComponents::plainImageUpload('favicon')
                        ->label(__('sn-cms::cms.general_setting.favicon'))
                        ->helperText(__('sn-cms::cms.general_setting.favicon_helper'))
                        ->imageAspectRatio('1:1')
                        ->directory(Utils::getFileDirectory('settings'))
                        ->uploadingMessage(__('sn-cms::cms.general_setting.favicon_uploading')),
                    FormComponents::plainImageUpload('homepage_banner')
                        ->label(__('sn-cms::cms.general_setting.homepage_banner'))
                        ->directory(Utils::getFileDirectory('settings'))
                        ->uploadingMessage(__('sn-cms::cms.general_setting.homepage_banner_uploading')),
                    FormComponents::plainImageUpload('default_og_image')
                        ->label(__('sn-cms::cms.general_setting.default_og_image'))
                        ->helperText(__('sn-cms::cms.general_setting.default_og_image_helper'))
                        ->imageAspectRatio('1.91:1')
                        ->directory(Utils::getFileDirectory('settings'))
                        ->uploadingMessage(__('sn-cms::cms.general_setting.default_og_image_uploading')),
                ])->columns(2),
                Schemas\Components\Section::make(__('sn-cms::cms.general_setting.seo_section'))->schema([
                    Forms\Components\Textarea::make('seo_description')
                        ->label(__('sn-cms::cms.general_setting.seo_description'))
                        ->helperText(__('sn-cms::cms.general_setting.seo_description_helper'))
                        ->rows(2)
                        ->maxLength(500),
                    Forms\Components\Textarea::make('analytics_code')
                        ->label(__('sn-cms::cms.general_setting.analytics_code'))
                        ->helperText(__('sn-cms::cms.general_setting.analytics_code_helper'))
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),
                Schemas\Components\Section::make(__('sn-cms::cms.general_setting.basic_info'))->schema([
                    Forms\Components\TextInput::make('wechat')
                        ->label(__('sn-cms::cms.general_setting.wechat')),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('sn-cms::cms.general_setting.phone')),
                    Forms\Components\TextInput::make('email')
                        ->label(__('sn-cms::cms.general_setting.email')),
                    Forms\Components\TextInput::make('address')
                        ->label(__('sn-cms::cms.general_setting.address')),
                    Forms\Components\TextInput::make('work_time')
                        ->label(__('sn-cms::cms.general_setting.work_time'))
                        ->helperText(__('sn-cms::cms.general_setting.work_time_helper')),
                ])->columns(2),
                Schemas\Components\Section::make(__('sn-cms::cms.general_setting.legal_section'))->schema([
                    Forms\Components\TextInput::make('copyright')
                        ->label(__('sn-cms::cms.general_setting.copyright')),
                    Forms\Components\TextInput::make('copytime')
                        ->label(__('sn-cms::cms.general_setting.copytime')),
                    Forms\Components\TextInput::make('beian_no')
                        ->label(__('sn-cms::cms.general_setting.beian_no')),
                    Forms\Components\TextInput::make('beian_url')
                        ->label(__('sn-cms::cms.general_setting.beian_url')),
                    Forms\Components\TextInput::make('beian_police_no')
                        ->label(__('sn-cms::cms.general_setting.beian_police_no')),
                    Forms\Components\TextInput::make('beian_police_url')
                        ->label(__('sn-cms::cms.general_setting.beian_police_url')),
                ])->columns(2),
                Schemas\Components\Section::make(__('sn-cms::cms.general_setting.qrcode_section'))->schema([
                    FormComponents::plainImageUpload('wechat_qrcode')
                        ->label(__('sn-cms::cms.general_setting.wechat_qrcode'))
                        ->directory(Utils::getFileDirectory('settings'))
                        ->uploadingMessage(__('sn-cms::cms.general_setting.wechat_qrcode_uploading')),
                    FormComponents::plainImageUpload('wechat_official_qrcode')
                        ->label(__('sn-cms::cms.general_setting.wechat_official_qrcode'))
                        ->directory(Utils::getFileDirectory('settings'))
                        ->uploadingMessage(__('sn-cms::cms.general_setting.wechat_official_qrcode_uploading')),
                ])->columns(2),
            ]);
    }

    public static function getEssentialsPlugin(): ?CmsPlugin
    {
        return CmsPlugin::get();
    }
}
