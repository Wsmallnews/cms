<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Schemas;

use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Alignment;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;
use Wsmallnews\Cms\Enums\NavigationStatus;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Facades\ContentRegistry;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Forms\FormComponents;

class NavigationForm
{
    public static function forms(array $arguments = []): array
    {
        $scopeType = $arguments['scope_type'] ?? '';

        return [
            Forms\Components\Select::make('type')
                ->helperText(fn (): ?HtmlString => new HtmlString('<span style="color: #F59E0B;">' . __('sn-cms::cms.navigation_form.type_helper') . '</span>'))
                ->label(__('sn-cms::cms.navigation_form.type'))
                ->options(NavigationTypeEnum::class)
                ->default(NavigationTypeEnum::Route)
                ->live()
                ->required(),
            Forms\Components\TextInput::make('name')->label(__('sn-cms::cms.navigation_form.name'))
                ->placeholder(__('sn-cms::cms.navigation_form.name_placeholder'))
                ->required(),
            Forms\Components\Textarea::make('description')->label(__('sn-cms::cms.navigation_form.description'))
                ->placeholder(__('sn-cms::cms.navigation_form.description_placeholder')),
            Forms\Components\ToggleButtons::make('options.icon_type')
                ->label(__('sn-cms::cms.navigation_form.icon_type'))
                ->options([
                    'none' => __('sn-cms::cms.navigation_form.icon_type_none'),
                    'icon' => __('sn-cms::cms.navigation_form.icon_type_icon'),
                    'image' => __('sn-cms::cms.navigation_form.icon_type_image'),
                ])
                ->default('none')
                ->inline(),
            Schemas\Components\Fieldset::make('icons')
                ->label(__('sn-cms::cms.navigation_form.icon_fieldset'))
                ->schema([
                    IconPicker::make('options.icon')->label(__('sn-cms::cms.navigation_form.icon'))
                        ->placeholder(__('sn-cms::cms.navigation_form.icon_placeholder'))
                        ->sets(['heroicons'])
                        ->iconsSearchResults(),
                    IconPicker::make('options.active_icon')->label(__('sn-cms::cms.navigation_form.active_icon'))
                        ->placeholder(__('sn-cms::cms.navigation_form.active_icon_placeholder'))
                        ->sets(['heroicons'])
                        ->iconsSearchResults(),
                ])
                ->visibleJs(<<<'JS'
                    $get('options.icon_type') == 'icon'
                JS),
            Schemas\Components\Fieldset::make('image_icons')
                ->label(__('sn-cms::cms.navigation_form.image_icon_fieldset'))
                ->schema([
                    FormComponents::plainImageUpload('options.icon_src')
                        ->label(__('sn-cms::cms.navigation_form.image_icon'))
                        ->directory(Utils::getFileDirectory('icons'))
                        ->automaticallyResizeImagesMode('cover')
                        ->imageAspectRatio('1:1')
                        ->automaticallyCropImagesToAspectRatio()
                        ->automaticallyResizeImagesToHeight('200')
                        ->automaticallyResizeImagesToWidth('200')
                        ->uploadingMessage(__('sn-cms::cms.navigation_form.image_icon_uploading')),
                    FormComponents::plainImageUpload('options.active_icon_src')
                        ->label(__('sn-cms::cms.navigation_form.active_image_icon'))
                        ->directory(Utils::getFileDirectory('icons'))
                        ->automaticallyResizeImagesMode('cover')
                        ->imageAspectRatio('1:1')
                        ->automaticallyCropImagesToAspectRatio()
                        ->automaticallyResizeImagesToHeight('200')
                        ->automaticallyResizeImagesToWidth('200')
                        ->uploadingMessage(__('sn-cms::cms.navigation_form.active_image_icon_uploading')),
                    Schemas\Components\Text::make(__('sn-cms::cms.navigation_form.image_tip'))
                        ->columnSpanFull(),

                ])
                ->visibleJs(<<<'JS'
                    $get('options.icon_type') == 'image'
                JS),
            Forms\Components\Toggle::make('options.footer_show')
                ->label(__('sn-cms::cms.navigation_form.footer_show'))
                ->default(false)
                ->helperText(__('sn-cms::cms.navigation_form.footer_show_helper'))
                ->inline(false)
                ->visible(function (Get $get) {
                    // 只有子菜单 可以设置底部显示
                    return in_array($get('type'), [NavigationTypeEnum::Child]);
                }),
            Forms\Components\TextInput::make('slug')
                ->label(__('sn-cms::cms.navigation_form.slug'))
                ->scopedUnique(modifyQueryUsing: function (Builder $query, Component $livewire) {
                    return $query->scopeable($livewire->getScopeType(), $livewire->getScopeId());
                })
                ->required()
                ->maxLength(255)
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置标识
                    return in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
                }),
            FormComponents::mediaImageUpload('navigation_banner', 'navigation_banner')
                ->label(__('sn-cms::cms.navigation_form.banner'))
                ->customProperties(function (Component $livewire) {
                    return [
                        ...$livewire->getScopeable(),
                        'team_id' => current_tenant()?->id,
                    ];
                })
                ->uploadingMessage(__('sn-cms::cms.navigation_form.banner_uploading'))
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置 Banner
                    return in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
                }),
            Forms\Components\Select::make('options.target')
                ->label(__('sn-cms::cms.navigation_form.target_type'))
                ->options([
                    '_self' => __('sn-cms::cms.navigation_form.target_self'),
                    '_blank' => __('sn-cms::cms.navigation_form.target_blank'),
                ])
                ->default('_self')
                ->visible(function (Get $get) {
                    // 没有子导航了，就显示跳转类型
                    return $get('type') != NavigationTypeEnum::Child;
                }),
            FormComponents::contentTypeGroup(
                types: Utils::getConfig('contents.navigation.types'),
                defaultType: Utils::getConfig('contents.navigation.default_type'),
                directory: Utils::getFileDirectory('contents'),
            )
                ->visible(function (Get $get) {
                    // page 页面设置页面详情
                    return $get('type') == NavigationTypeEnum::Page;
                }),
            Forms\Components\TextInput::make('options.url')
                ->label(__('sn-cms::cms.navigation_form.url'))
                ->placeholder(__('sn-cms::cms.navigation_form.url_placeholder'))
                ->required()
                ->visible(function (Get $get) {
                    // Url 类型显示 跳转链接
                    return $get('type') == NavigationTypeEnum::Url;
                }),
            Forms\Components\TextInput::make('options.route')
                ->label(__('sn-cms::cms.navigation_form.route_name'))
                ->placeholder(__('sn-cms::cms.navigation_form.route_name_placeholder'))
                ->required()
                ->visible(function (Get $get) {
                    // 跳转路由,填写路由名称
                    return $get('type') == NavigationTypeEnum::Route;
                }),
            Schemas\Components\Fieldset::make('url_params')
                ->label(__('sn-cms::cms.navigation_form.url_params'))
                ->schema([
                    Schemas\Components\Group::make()
                        ->schema([
                            Forms\Components\Toggle::make('has_routes')
                                ->label(__('sn-cms::cms.navigation_form.route_param'))
                                ->default(false)
                                ->helperText(__('sn-cms::cms.navigation_form.route_param_helper')),
                            Forms\Components\KeyValue::make('routes')
                                ->label(__('sn-cms::cms.navigation_form.route_params'))
                                ->helperText(__('sn-cms::cms.navigation_form.route_params_helper'))
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
                                ->label(__('sn-cms::cms.navigation_form.query_param'))
                                ->default(false)
                                ->helperText(__('sn-cms::cms.navigation_form.query_param_helper')),
                            Forms\Components\KeyValue::make('queries')
                                ->label(__('sn-cms::cms.navigation_form.query_params'))
                                ->helperText(__('sn-cms::cms.navigation_form.query_params_helper'))
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
                ->label(__('sn-cms::cms.navigation_form.custom_content'))
                ->schema(function () use ($scopeType) {
                    $uuid = Str::uuid();

                    return [
                        Forms\Components\Select::make('type')
                            ->label(__('sn-cms::cms.navigation_form.content_type'))
                            ->placeholder(__('sn-cms::cms.navigation_form.content_type_placeholder'))
                            ->options(ContentRegistry::getTypesOptions($scopeType))
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Forms\Components\Select $component, $state, Set $set) use ($uuid, $scopeType) {
                                // 默认设置内容类型 label
                                $set('label', ContentRegistry::getTypesOptions($scopeType)[$state] ?? '');

                                // 填充组件特定字段
                                return $state && $component
                                    ->getContainer()
                                    ->getComponent('dynamicExtrasFields_' . $uuid)       // 当 dynamicExtrasFields visible = false, 也就是不可见时， 这里获取的是 null
                                    ?->getChildSchema()
                                    ->fill();
                            }),

                        // 显示 type 对应的 label
                        Forms\Components\TextInput::make('label')
                            ->label(__('sn-cms::cms.navigation_form.content_name'))
                            ->live(onBlur: true)
                            ->placeholder(__('sn-cms::cms.navigation_form.content_name_placeholder')),

                        Schemas\Components\Fieldset::make('extras')
                            ->label(__('sn-cms::cms.navigation_form.content_options'))
                            ->schema(function (Get $get) use ($scopeType) {
                                return filled($get('type')) ? ContentRegistry::getTypeForms($scopeType, $get('type'), ['fields' => $get('../../../')]) : [];        // $get() 获取的为当前repeater 循环层级的数据，需要 ../../../ 获取所有变量
                            })->visible(function (Get $get) use ($scopeType) {
                                $hasForms = filled($get('type')) ? ContentRegistry::hasTypeForms($scopeType, $get('type'), ['fields' => $get('../../../')]) : false;    // $get() 获取的为当前repeater 循环层级的数据，需要 ../../../ 获取所有变量

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
                ->addActionLabel(__('sn-cms::cms.navigation_form.add_group'))
                ->collapsible()
                ->cloneable()
                ->addActionAlignment(Alignment::Start)
                ->columns(['md' => 2])
                ->visible(function (Get $get) {
                    return $get('type') == NavigationTypeEnum::Content;
                })
                ->statePath('options.components'),

            Forms\Components\Radio::make('status')
                ->label(__('sn-cms::cms.navigation_form.status'))
                ->inline()
                ->options(NavigationStatus::class)
                ->default(NavigationStatus::Normal),
        ];
    }
}
