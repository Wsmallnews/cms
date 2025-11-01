<?php

namespace Wsmallnews\Cms\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Support\Enums\Traits\EnumHelper;

enum NavigationTypeStatus: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Normal = 'normal';

    case Disabled = 'disabled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Normal => '正常',
            self::Disabled => '禁用',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Normal => 'success',
            self::Disabled => 'gary',
        };
    }

    public function getIcon(): string | BackedEnum | null
    {
        return match ($this) {
            self::Normal => Heroicon::OutlinedEye,
            self::Disabled => Heroicon::OutlinedNoSymbol,
        };
    }
}
