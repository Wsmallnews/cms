<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Schemas;

use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Alignment;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
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
                ->helperText(fn (): ?HtmlString => new HtmlString('<span style="color: #F59E0B;">如果存在子导航，当前导航设置的 跳转链接/路由等将失效</span>'))
                ->label('导航类型')
                ->options(NavigationTypeEnum::class)
                ->default(NavigationTypeEnum::Route)
                ->live()
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
            Schemas\Components\Fieldset::make('icons')
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
            Schemas\Components\Fieldset::make('image_icons')
                ->label('图片图标')
                ->schema([
                    Forms\Components\FileUpload::make('options.icon_src')
                        ->label('图标')
                        ->image()
                        ->disk(SupportUtils::getFilesystemDisk())
                        ->directory(Utils::getFileDirectory('icons'))
                        ->visibility('public')
                        ->automaticallyResizeImagesMode('cover')
                        ->imageAspectRatio('1:1')
                        ->automaticallyCropImagesToAspectRatio()
                        ->automaticallyResizeImagesToHeight('200')
                        ->automaticallyResizeImagesToWidth('200')
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
                        ->automaticallyResizeImagesMode('cover')
                        ->imageAspectRatio('1:1')
                        ->automaticallyCropImagesToAspectRatio()
                        ->automaticallyResizeImagesToHeight('200')
                        ->automaticallyResizeImagesToWidth('200')
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
                ->visible(function (Get $get) {
                    // 只有子菜单 可以设置底部显示
                    return in_array($get('type'), [NavigationTypeEnum::Child]);
                }),
            Forms\Components\TextInput::make('slug')
                ->label('导航标识')
                // @sn todo 导航标识唯一性需要附加条件
                ->unique(ignorable: fn (?NavigationModel $record): ?NavigationModel => $record)
                ->required()
                ->maxLength(255)
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置标识
                    return in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
                }),
            Forms\Components\SpatieMediaLibraryFileUpload::make('navigation_banner')
                ->label('导航Banner')
                ->collection('navigation_banner')
                ->image()
                ->disk(SupportUtils::getFilesystemDisk())
                ->visibility('public')
                ->customProperties(function (Component $livewire) {
                    return [
                        ...$livewire->getScopeable(),
                        'team_id' => current_tenant()?->id,
                    ];
                })
                ->openable()
                ->downloadable()
                ->uploadingMessage('Banner 上传中...')
                ->imagePreviewHeight('200')
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置 Banner
                    return in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
                }),
            Forms\Components\Select::make('options.target')
                ->label('跳转类型')
                ->options([
                    '_self' => '当前窗口',
                    '_blank' => '新窗口',
                ])
                ->default('_self')
                ->visible(function (Get $get) {
                    // 没有子导航了，就显示跳转类型
                    return $get('type') != NavigationTypeEnum::Child;
                }),
            Schemas\Components\Group::make()
                ->schema([
                    Schemas\Components\Group::make()
                        ->relationship('content')
                        ->schema([
                            Forms\Components\RichEditor::make('content')
                                ->label('页面内容详情')
                                ->fileAttachmentsDirectory('contents/' . date('Ymd'))
                                ->required(),
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(function (Get $get) {
                    // page 页面设置页面详情
                    return $get('type') == NavigationTypeEnum::Page;
                }),
            Forms\Components\TextInput::make('options.url')
                ->label('跳转链接')
                ->placeholder('请输入跳转链接')
                ->required()
                ->visible(function (Get $get) {
                    // Url 类型显示 跳转链接
                    return $get('type') == NavigationTypeEnum::Url;
                }),
            Forms\Components\TextInput::make('options.route')
                ->label('路由名称')
                ->placeholder('请输入路由名称')
                ->required()
                ->visible(function (Get $get) {
                    // 跳转路由,填写路由名称
                    return $get('type') == NavigationTypeEnum::Route;
                }),
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
                ->visible(function (Get $get) {
                    // 内容类型的导航，选了内容类型，并且内容类型有 form 表单
                    return $get('type') == NavigationTypeEnum::Route;
                }),
            Forms\Components\Repeater::make('contentComponents')
                ->label('自定义内容')
                ->schema(function () use ($arguments) {
                    $uuid = Str::uuid();

                    return [
                        Forms\Components\Select::make('type')
                            ->label('内容类型')
                            ->placeholder('请选择内容类型')
                            ->options(ContentRegistry::getTypesOptions($arguments['scope_type']))
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Forms\Components\Select $component, $state, Set $set) use ($uuid, $arguments) {
                                // 默认设置内容类型 label
                                $set('label', ContentRegistry::getTypesOptions($arguments['scope_type'])[$state] ?? '');

                                // 填充组件特定字段
                                return $state && $component
                                    ->getContainer()
                                    ->getComponent('dynamicExtrasFields_' . $uuid)       // 当 dynamicExtrasFields visible = false, 也就是不可见时， 这里获取的是 null
                                    ?->getChildSchema()
                                    ->fill();
                            }),

                        // 显示 type 对应的 label
                        Forms\Components\TextInput::make('label')
                            ->label('内容名称')
                            ->live(onBlur: true)
                            ->placeholder('请输入内容名称'),

                        Schemas\Components\Fieldset::make('extras')
                            ->label('选项')
                            ->schema(function (Get $get) use ($arguments) {
                                return filled($get('type')) ? ContentRegistry::getTypeForms($arguments['scope_type'], $get('type'), ['fields' => $get('../../../')]) : [];        // $get() 获取的为当前repeater 循环层级的数据，需要 ../../../ 获取所有变量
                            })->visible(function (Get $get) use ($arguments) {
                                $hasForms = filled($get('type')) ? ContentRegistry::hasTypeForms($arguments['scope_type'], $get('type'), ['fields' => $get('../../../')]) : false;    // $get() 获取的为当前repeater 循环层级的数据，需要 ../../../ 获取所有变量

                                // 选了内容类型，并且内容类型有 form 表单
                                return filled($get('type')) && $hasForms;
                            })
                            ->columns(['md' => 2])
                            ->columnSpanFull()
                            ->statePath('extras')
                            ->key('dynamicExtrasFields_' . $uuid),
                    ];
                })
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->required()
                ->minItems(1)
                ->addActionLabel('添加分组')
                ->collapsible()
                ->cloneable()
                ->addActionAlignment(Alignment::Start)
                ->columns(['md' => 2])
                ->visible(function (Get $get) {
                    return $get('type') == NavigationTypeEnum::Content;
                })
                ->statePath('options.components'),

            Forms\Components\Radio::make('status')
                ->label('导航状态')
                ->inline()
                ->options(NavigationStatus::class)
                ->default(NavigationStatus::Normal),
        ];
    }
}
