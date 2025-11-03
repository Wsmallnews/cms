<?php

namespace Wsmallnews\Cms\Filament\Resources\NavigationTypes\Schemas;

use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Wsmallnews\Cms\Enums\NavigationTypeStatus;

class NavigationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...static::forms(),
            ]);
    }

    public static function forms(): array
    {
        return [
            Schemas\Components\Flex::make([
                Schemas\Components\Group::make()->schema([
                    Schemas\Components\Section::make('基础信息')->schema([
                        Forms\Components\TextInput::make('name')->label('导航名称')
                            ->placeholder('请输入导航名称')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Radio::make('level')->label('层级')
                            ->options([
                                1 => '一级',
                                2 => '二级',
                                3 => '三级',
                                'infinite' => '无限级',
                            ])
                            ->formatStateUsing(fn($state) => is_null($state) ? 'infinite' : $state)     // 显示时，null 转成 infinite
                            ->dehydrateStateUsing(fn($state) => $state === 'infinite' ? null : $state)  // 存库时，infinite 转成 null
                            ->default(1)
                            ->inline()
                            ->required()
                            ->helperText(fn ($operation) => $operation === 'create' ? '请认真设置自己需要的层级' : '修改层级将导致嵌套集层级异常, 请谨慎修改')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('description')->label('类别描述')
                            ->placeholder('请输入类别描述')
                            ->columnSpan(1),
                    ])->columns(2),
                ])->columns(1),
                Schemas\Components\Section::make('状态')->schema([
                    Forms\Components\TextInput::make('order_column')->label('排序')->integer()
                        ->placeholder('正序排列')
                        ->rules(['integer', 'min:0']),
                    Forms\Components\Radio::make('status')
                        ->label('状态')
                        ->default(NavigationTypeStatus::Normal)
                        ->inline()
                        ->options(NavigationTypeStatus::class),
                ])->grow(false),
            ])
                ->columnSpanFull()
                ->from('lg'),
        ];
    }
}
