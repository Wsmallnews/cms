<?php

namespace Wsmallnews\Cms\Filament\Pages;

use BackedEnum;
use BezhanSalleh\PluginEssentials\Concerns;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Support\Concerns\Resource\HasCustomProperties;

class GeneralSetting extends SettingsPage
{
    use Concerns\Resource\BelongsToParent;
    use Concerns\Resource\BelongsToTenant;
    use Concerns\Resource\HasGlobalSearch;
    use Concerns\Resource\HasLabels;
    use Concerns\Resource\HasNavigation;
    use HasCustomProperties;

    protected static ?string $title = '基础设置';

    protected static ?string $slug = 'general-settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('基础信息')->schema([
                    Forms\Components\TextInput::make('wechat')
                        ->label('官方微信号'),
                    Forms\Components\TextInput::make('phone')
                        ->label('联系电话'),
                    Forms\Components\TextInput::make('email')
                        ->label('邮箱'),
                    Forms\Components\TextInput::make('address')
                        ->label('地址'),
                    Forms\Components\TextInput::make('copyright')
                        ->label('版权信息'),
                    Forms\Components\TextInput::make('copytime')
                        ->label('版权时间'),
                    Forms\Components\TextInput::make('beian_no')
                        ->label('备案号'),
                    Forms\Components\TextInput::make('beian_url')
                        ->label('工信部网址'),
                ])->columns(2),
                Schemas\Components\Section::make('二维码上传')->schema([
                    Forms\Components\FileUpload::make('wechat_qrcode')->label('微信二维码')
                        ->directory('settings/general')
                        ->openable()
                        ->image()
                        ->downloadable()
                        ->uploadingMessage('微信二维码上传中...')
                        ->imagePreviewHeight('100'),
                    Forms\Components\FileUpload::make('wechat_official_qrcode')->label('公众号二维码')
                        ->directory('settings/general')
                        ->openable()
                        ->image()
                        ->downloadable()
                        ->uploadingMessage('公众号二维码上传中...')
                        ->imagePreviewHeight('100'),
                ])->columns(2)
            ]);
    }
}
