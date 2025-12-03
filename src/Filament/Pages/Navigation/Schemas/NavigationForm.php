<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Schemas;

use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Guava\IconPicker\Forms\Components\IconPicker;
use Livewire\Component;
use Wsmallnews\Cms\Enums\NavigationStatus;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Facades\ContentRegistry;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class NavigationForm
{
    public static function forms(array $arguments = []): array
    {
        return [
            Forms\Components\Select::make('type')
                // ->helperText('如果存在子导航，父导航设置的 跳转链接/路由等将失效')
                ->label('导航类型')
                ->options(NavigationTypeEnum::class)
                ->default(NavigationTypeEnum::Route)
                ->required(),
            Forms\Components\TextInput::make('name')->label('导航名称')
                ->placeholder('请输入导航名称')
                ->required(),
            Forms\Components\Textarea::make('description')->label('描述')
                ->placeholder('请输入导航描述'),
            Forms\Components\ToggleButtons::make('options.icon_type')
                ->label('导航图标')
                ->options([
                    'none' => '无图标',
                    'icon' => 'icon图标',
                    'image' => '图片图标',
                ])
                ->default('none')
                ->inline(),
            Schemas\Components\FieldSet::make('icons')
                ->label('icon 图标')
                ->schema([
                    IconPicker::make('options.icon')->label('图标')
                        ->placeholder('请选择图标')
                        ->sets(['heroicons'])
                        ->iconsSearchResults(),
                    IconPicker::make('options.active_icon')->label('活动图标')
                        ->placeholder('请选择活动图标')
                        ->sets(['heroicons'])
                        ->iconsSearchResults(),
                ])
                ->visibleJs(<<<'JS'
                    $get('options.icon_type') == 'icon'
                JS),
            Schemas\Components\FieldSet::make('image_icons')
                ->label('图片图标')
                ->schema([
                    Forms\Components\FileUpload::make('options.icon_src')
                        ->label('图标')
                        ->image()
                        ->disk(SupportUtils::getFilesystemDisk())
                        ->directory(Utils::getFileDirectory('icons'))
                        ->visibility('public')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetHeight('200')
                        ->imageResizeTargetWidth('200')
                        ->openable()
                        ->downloadable()
                        ->uploadingMessage('图标上传中...')
                        ->imagePreviewHeight('100'),
                    Forms\Components\FileUpload::make('options.active_icon_src')
                        ->label('活动图标')
                        ->image()
                        ->disk(SupportUtils::getFilesystemDisk())
                        ->directory(Utils::getFileDirectory('icons'))
                        ->visibility('public')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetHeight('200')
                        ->imageResizeTargetWidth('200')
                        ->openable()
                        ->downloadable()
                        ->uploadingMessage('活动图标上传中...')
                        ->imagePreviewHeight('100'),
                    Schemas\Components\Text::make('请上传正方形图片，推荐大小为 200x200 像素，非正方形图片将被自动缩放裁剪')
                        ->columnSpanFull(),

                ])
                ->visibleJs(<<<'JS'
                    $get('options.icon_type') == 'image'
                JS),
            Forms\Components\Toggle::make('options.footer_show')
                ->label('底部显示')
                ->default(false)
                ->helperText('如果开启底部显示，则在页面底部显示该导航')
                ->inline(false)
                ->visibleJs(<<<'JS'
                    // 只有子菜单 可以设置底部显示
                    ['child'].includes($get('type'))
                JS),
            Forms\Components\TextInput::make('slug')
                ->label('导航标识')
                // @sn todo 导航标识唯一性需要附加条件
                ->unique(ignorable: fn (?NavigationModel $record): ?NavigationModel => $record)
                ->required(fn (Get $get) => in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]))
                ->markAsRequired()
                ->maxLength(255)
                ->visibleJs(<<<'JS'
                    // 只有内容 和 页面 需要设置标识
                    ['page', 'content'].includes($get('type'))
                JS),
            Forms\Components\SpatieMediaLibraryFileUpload::make('navigation_banner')
                ->label('导航Banner')
                ->collection('navigation_banner')
                ->image()
                ->disk(SupportUtils::getFilesystemDisk())
                ->visibility('public')
                ->customProperties(function (Component $livewire) {
                    return [
                        ...$livewire->getScopeable(),
                        'team_id' => general_current_tenant()?->id,
                    ];
                })
                ->openable()
                ->downloadable()
                ->uploadingMessage('Banner 上传中...')
                ->imagePreviewHeight('200')
                ->visibleJs(<<<'JS'
                    ['page', 'content'].includes($get('type'))
                JS),
            Forms\Components\Select::make('options.target')
                ->label('跳转类型')
                ->options([
                    '_self' => '当前窗口',
                    '_blank' => '新窗口',
                ])
                ->default('_self')
                ->visibleJs(<<<'JS'
                    !['child'].includes($get('type'))
                JS),
            Schemas\Components\Group::make()
                ->schema([
                    Schemas\Components\FieldSet::make('contentView')
                        ->label('自定义视图')
                        ->schema([
                            Forms\Components\Toggle::make('options._content_views.hasCustomView')
                                ->label('自定义视图')
                                ->default(false)
                                ->helperText('开启自定义视图, 将会使用自定义视图')
                                ->inline(false),
                            Forms\Components\TextInput::make('options._content_views.view')->label('自定义视图')
                                ->placeholder('请输入自定义视图地址')
                                ->required(fn (Get $get) => (bool) $get('options._content_views.hasCustomView'))
                                ->markAsRequired()
                                ->visibleJs(<<<'JS'
                                    $get('options._content_views.hasCustomView')
                                JS),
                        ])->columns(1),
                    Schemas\Components\FieldSet::make('contentBlockContainer')
                        ->label('容器包装器')
                        ->schema([
                            Forms\Components\Toggle::make('options._content_block_container.hasDefaultBlockContainerWrapper')
                                ->label('容器包装器')
                                ->default(false)
                                ->helperText('开启容器包装器, 将会在组件外包一层包装器')
                                ->inline(false),
                            Forms\Components\TextInput::make('options._content_block_container.blockContainerWrapperView')->label('自定义包装器')
                                ->placeholder('不填写将使用默认包装器')
                                ->helperText('如果不填写将使用默认包装器')
                                ->visibleJs(<<<'JS'
                                    $get('options._content_block_container.hasDefaultBlockContainerWrapper')
                                JS),
                        ])->columns(1),
                    Schemas\Components\Group::make()
                        ->relationship('content')
                        ->schema([
                            Forms\Components\RichEditor::make('content')
                                ->label('页面内容详情')
                                ->fileAttachmentsDirectory('contents/' . date('Ymd'))
                                ->required(fn(Get $get) => in_array($get('../type'), [NavigationTypeEnum::Page]))
                                ->markAsRequired(),
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visibleJs(<<<'JS'
                    ['page'].includes($get('type'))
                JS),
            Forms\Components\TextInput::make('options.url')
                ->label('跳转链接')
                ->placeholder('请输入跳转链接')
                ->required(fn (Get $get) => in_array($get('type'), [NavigationTypeEnum::Url]))
                ->markAsRequired()
                ->visibleJs(<<<'JS'
                    ['url'].includes($get('type'))
                JS),
            Forms\Components\TextInput::make('options.route')
                ->label('路由名称')
                ->placeholder('请输入路由名称')
                ->required(fn (Get $get) => in_array($get('type'), [NavigationTypeEnum::Route]))
                ->markAsRequired()
                ->visibleJs(<<<'JS'
                    ['route'].includes($get('type'))
                JS),
            Schemas\Components\Fieldset::make('url_params')
                ->label('请求参数')
                ->schema([
                    Schemas\Components\Group::make()
                        ->schema([
                            Forms\Components\Toggle::make('has_routes')
                                ->label('路由参数')
                                ->default(false)
                                ->helperText('如果有路由参数，则开启当前选项'),
                            Forms\Components\KeyValue::make('routes')
                                ->label('路由参数')
                                ->helperText('路由参数, 没有则不设置')
                                ->reorderable()
                                ->required(fn (Get $get) => (bool) $get('has_routes'))
                                ->markAsRequired()
                                ->visibleJs(<<<'JS'
                                    $get('has_routes')
                                JS),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    Schemas\Components\Group::make()
                        ->schema([
                            Forms\Components\Toggle::make('has_queries')
                                ->label('查询参数')
                                ->default(false)
                                ->helperText('如果有查询参数，则开启当前选项'),
                            Forms\Components\KeyValue::make('queries')
                                ->label('查询参数')
                                ->helperText('查询参数, 拼接在地址栏后面, 没有则不设置')
                                ->reorderable()
                                ->required(fn (Get $get) => (bool) $get('has_queries'))
                                ->markAsRequired()
                                ->visibleJs(<<<'JS'
                                    $get('has_queries')
                                JS),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                ])
                ->columns(2)
                ->statePath('options._url_params')
                ->visibleJs(<<<'JS'
                    ['route'].includes($get('type'))
                JS),
            Forms\Components\Select::make('options.type')
                ->label('内容类型')
                ->placeholder('请选择内容类型')
                ->options(ContentRegistry::getOptions())
                ->live()
                ->required(fn (Get $get) => in_array($get('type'), [NavigationTypeEnum::Content]))
                ->markAsRequired()
                ->visibleJs(<<<'JS'
                    ['content'].includes($get('type'))
                JS)
                ->afterStateUpdated(
                    fn (Forms\Components\Select $component, $state) => $state && $component
                        ->getContainer()
                        ->getComponent('dynamicExtrasFields')       // 当 dynamicExtrasFields visible = false, 也就是不可见时， 这里获取的是 null
                        ?->getChildSchema()
                        ->fill()
                ),

            Schemas\Components\Fieldset::make('extras')
                ->label('选项')
                ->schema(function (Get $get) {
                    return filled($get('options.type')) ? ContentRegistry::getTypeForms($get('options.type'), ['fields' => $get()]) : [];
                })->visible(function (Get $get) {
                    $hasForms = filled($get('options.type')) ? ContentRegistry::hasForms($get('options.type'), ['fields' => $get()]) : false;

                    // 内容类型的导航，选了内容类型，并且内容类型有 form 表单
                    return ($get('type') == NavigationTypeEnum::Content) && filled($get('options.type')) && $hasForms;
                })
                ->statePath('options._extras')
                ->key('dynamicExtrasFields'),

            Forms\Components\Radio::make('status')
                ->label('导航状态')
                ->inline()
                ->options(NavigationStatus::class)
                ->default(NavigationStatus::Normal),
        ];
    }
}
