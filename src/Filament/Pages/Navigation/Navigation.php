<?php

namespace App\Filament\Pages;

use App\Enums\Navigations\Status;
use App\Enums\Navigations\Type as NavigationTypeEnum;
use App\Features\NavigationType;
use App\Models\Navigation as NavigationModel;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Kalnoy\Nestedset\QueryBuilder;
use UnitEnum;
use Wsmallnews\FilamentNestedset\Pages\NestedsetPage;

class Navigation extends NestedsetPage
{
    use HasPageShield;

    public string $emptyLabel = '导航数据为空';

    protected static ?string $model = NavigationModel::class;

    protected static ?string $modelLabel = '导航管理';

    protected static ?string $title = '导航管理';

    protected static ?string $navigationLabel = '导航管理';

    protected static string | UnitEnum | null $navigationGroup = '网站管理';

    protected static ?string $slug = 'navigations';

    protected static string $recordTitleAttribute = 'name';

    protected static ?string $pluralModelLabel = '导航管理';

    protected static ?int $navigationSort = 1;

    public function createSchema($arguments): array
    {
        return $this->schema($arguments);
    }

    public function editSchema($arguments): array
    {
        return $this->schema($arguments);
    }

    public function infolistSchema(): array
    {
        return [
            Infolists\Components\TextEntry::make('description')
                ->label('描述')
                ->visible(fn($state): bool => $state ? true : false),
            Infolists\Components\IconEntry::make('status')
                ->label('状态'),
        ];
    }



    protected function schema(array $arguments): array
    {
        return [
            Forms\Components\Select::make('type')
                // ->helperText('如果存在子导航，父导航设置的 跳转链接/路由等将失效')
                ->label('导航类型')
                ->options(NavigationTypeEnum::class)
                ->default(NavigationTypeEnum::Route)
                ->live()
                ->required(),
            Forms\Components\TextInput::make('name')->label('导航名称')
                ->placeholder('请输入导航名称')
                ->required(),
            Forms\Components\Textarea::make('description')->label('描述'),

            Forms\Components\Toggle::make('options.footer_show')
                ->label('底部显示')
                ->default(false)
                ->helperText('如果开启底部显示，则在底部显示该导航')
                ->required()
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置标识
                    return in_array($get('type'), [NavigationTypeEnum::Child]);
                }),
            Forms\Components\TextInput::make('slug')
                ->label('导航标识')
                ->unique(ignorable: fn(?NavigationModel $record): ?NavigationModel => $record)
                ->required()
                ->maxLength(255)
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置标识
                    return in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
                }),

            Forms\Components\SpatieMediaLibraryFileUpload::make('banner')->label('导航Banner')
                ->collection('banner')
                ->image()
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
                ->required()
                ->visible(function (Get $get) {
                    // 没有子导航了，就显示跳转类型
                    return $get('type') != NavigationTypeEnum::Child;
                }),

            Schemas\Components\Group::make()
                ->relationship('content')
                ->schema([
                    Forms\Components\RichEditor::make('content')
                        ->fileAttachmentsDirectory('contents/' . date('Ymd'))
                        ->label('内容详情')
                        ->required(),
                ])
                ->visible(function (Get $get) {
                    // 没有子导航了，就显示跳转类型
                    return $get('type') == NavigationTypeEnum::Page;
                }),

            Forms\Components\TextInput::make('options.url')
                ->label('跳转链接')
                ->required()
                ->visible(function (Get $get) {
                    // Url 类型显示 跳转链接
                    return $get('type') == NavigationTypeEnum::Url;
                }),

            Forms\Components\TextInput::make('options.route')
                ->label('路由名称')
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
                                ->helperText('如果有路由参数，则开启当前选项')
                                ->required()
                                ->live(),
                            Forms\Components\KeyValue::make('routes')
                                ->label('路由参数')
                                ->helperText('路由参数, 没有则不设置')
                                ->reorderable()
                                ->required()
                                ->visible(fn(Get $get): bool => $get('has_routes')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    Schemas\Components\Group::make()
                        ->schema([
                            Forms\Components\Toggle::make('has_queries')
                                ->label('查询参数')
                                ->default(false)
                                ->helperText('如果有查询参数，则开启当前选项')
                                ->required()
                                ->live(),
                            Forms\Components\KeyValue::make('queries')
                                ->label('查询参数')
                                ->helperText('查询参数, 拼接在地址栏后面, 没有则不设置')
                                ->reorderable()
                                ->required()
                                ->visible(fn (Get $get): bool => $get('has_queries')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                ])->visible(function (Get $get) {
                    // 内容类型的导航，选了内容类型，并且内容类型有 form 表单
                    return $get('type') == NavigationTypeEnum::Route;
                })
                ->columns(2)
                ->statePath('options._url_params'),

            Forms\Components\Select::make('options.type')
                ->label('内容类型')
                ->options(NavigationType::make()->getOptions())
                ->live()
                ->required()
                ->visible(function (Get $get) {
                    return $get('type') == NavigationTypeEnum::Content;
                })
                ->afterStateUpdated(fn (Forms\Components\Select $component, $state) => $state && $component
                    ->getContainer()
                    ->getComponent('dynamicExtrasFields')       // 当 dynamicExtrasFields visible = false, 也就是不可见时， 这里获取的是 null
                    ?->getChildSchema()
                    ->fill()
                ),

            Schemas\Components\Fieldset::make('extras')
                ->label('选项')
                ->schema(function (Get $get) {
                    return NavigationType::make()->getTypeForms($get('options.type'), ['fields' => $get()]);
                })->visible(function (Get $get) {
                    $hasForms = NavigationType::make()->hasForms($get('options.type'), ['fields' => $get()]);

                    // 内容类型的导航，选了内容类型，并且内容类型有 form 表单
                    return ($get('type') == NavigationTypeEnum::Content) && filled($get('options.type')) && $hasForms;
                })
                ->statePath('options._extras')
                ->key('dynamicExtrasFields'),

            Forms\Components\Radio::make('status')
                ->label('导航状态')
                ->inline()
                ->options(Status::class)
                ->default(Status::Normal)
                ->required()
        ];
    }
}
