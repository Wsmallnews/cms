<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Schemas;

use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Wsmallnews\Cms\Enums\NavigationStatus;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Forms\FormComponents;

class NavigationForm
{
    public static function forms(array $arguments = []): array
    {
        return [
            // 首页标记：勾选后导航类型自动切换并锁定为内容类型，且必须绑定内容编排；
            // 同 scope 内互斥（模型 saving 钩子保证），原首页节点自动退回普通内容页（仅地址变化）
            Forms\Components\Toggle::make('options.is_home')
                ->label(__('sn-cms::cms.navigation_form.is_home'))
                ->helperText(__('sn-cms::cms.navigation_form.is_home_helper'))
                ->live()
                ->columnSpanFull()
                ->afterStateUpdated(function (Set $set, $state) {
                    if ($state) {
                        // 枚举 Select 的 state cast 会双向归一（写入枚举/字符串等价）；数据层由模型 saving 钩子兜底强制
                        $set('type', NavigationTypeEnum::Content);

                        // 清空此前已选的父级：包的创建链路用 `??` 取 parent_id（null 会被当作缺失，
                        // 回退到 createChild 动作传入的 parentId），故置 0（无父级 = 根节点）
                        $set('parent_id', 0);
                    }
                }),
            Forms\Components\Select::make('type')
                ->helperText(fn (): ?HtmlString => new HtmlString('<span style="color: #F59E0B;">' . __('sn-cms::cms.navigation_form.type_helper') . '</span>'))
                ->label(__('sn-cms::cms.navigation_form.type'))
                ->options(NavigationTypeEnum::class)
                ->default(NavigationTypeEnum::Route)
                ->live()
                ->required()
                ->disabled(fn (Get $get): bool => (bool) $get('options.is_home')),
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
                ->inline()->grouped(),
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
            Forms\Components\TextInput::make('slug')
                ->label(__('sn-cms::cms.navigation_form.slug'))
                ->scopedUnique(modifyQueryUsing: function (Builder $query, Component $livewire) {
                    return $query->scopeable($livewire->getScopeType(), $livewire->getScopeId());
                })
                ->required()
                ->maxLength(255)
                ->visible(function (Get $get) {
                    // 只有内容 和 页面 需要设置标识；首页节点入口指向模块首页，无需标识
                    return ! $get('options.is_home') && in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
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
                    // 只有内容 和 页面 需要设置 Banner；首页节点由首页编排渲染，不走导航容器
                    return ! $get('options.is_home') && in_array($get('type'), [NavigationTypeEnum::Page, NavigationTypeEnum::Content]);
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
            // 内容类型 = 引用内容编排（Composition）；编排内的组件在「内容编排」资源中维护
            Forms\Components\Select::make('options.composition_id')
                ->label(__('sn-cms::cms.navigation_form.composition'))
                ->placeholder(__('sn-cms::cms.navigation_form.composition_placeholder'))
                ->options(fn (): array => Utils::getCompositionModel()::query()
                    ->published()
                    ->snScope(...Utils::getScopeable())
                    ->limit(30)
                    ->pluck('title', 'id')
                    ->toArray())
                ->getSearchResultsUsing(fn (string $search): array => Utils::getCompositionModel()::query()
                    ->published()
                    ->snScope(...Utils::getScopeable())
                    ->where('title', 'like', "%{$search}%")
                    ->limit(30)
                    ->pluck('title', 'id')
                    ->toArray())
                ->searchable()
                ->preload()
                // 勾选首页标记后必须绑定编排
                ->required(fn (Get $get): bool => (bool) $get('options.is_home'))
                ->markAsRequired(fn (Get $get): bool => (bool) $get('options.is_home'))
                ->visible(function (Get $get) {
                    return $get('type') == NavigationTypeEnum::Content;
                }),

            FormComponents::enumsToggleButtons(NavigationStatus::class)
                ->label(__('sn-cms::cms.navigation_form.status')),
        ];
    }
}
