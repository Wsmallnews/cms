<?php

namespace Wsmallnews\Cms\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Wsmallnews\Support\Enums\Traits\EnumHelper;

enum NavigationTypeStatus: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Normal = 'normal';

    case Disabled = 'disabled';

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            self::Normal => __('sn-cms::cms.navigation_type_status.normal'),
            self::Disabled => __('sn-cms::cms.navigation_type_status.disabled'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Normal => 'success',
            self::Disabled => 'danger',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::Normal => Heroicon::OutlinedEye,
            self::Disabled => Heroicon::OutlinedNoSymbol,
        };
    }
}
