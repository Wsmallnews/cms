<?php

namespace Wsmallnews\Cms\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Wsmallnews\Support\Enums\Traits\EnumHelper;

enum NavigationStatus: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Normal = 'normal';

    case Hidden = 'hidden';

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            self::Normal => __('sn-cms::cms.navigation_status.normal'),
            self::Hidden => __('sn-cms::cms.navigation_status.hidden'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Normal => 'success',
            self::Hidden => 'gray',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::Normal => Heroicon::OutlinedEye,
            self::Hidden => Heroicon::OutlinedEyeSlash,
        };
    }
}
