<?php

namespace Wsmallnews\Cms\Enums;

use BackedEnum;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Wsmallnews\Cms\Contracts\PostFlagContract;
use Wsmallnews\Support\Enums\Traits\EnumHelper;

enum PostFlag: string implements PostFlagContract, HasLabel, HasColor, HasIcon
{
    use EnumHelper;

    case Hot = PostFlagContract::HOT;

    case New = PostFlagContract::NEW;

    case Recommend = PostFlagContract::RECOMMEND;

    case Top = PostFlagContract::TOP;

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            self::Hot => __('sn-cms::cms.flags.hot'),
            self::New => __('sn-cms::cms.flags.new'),
            self::Recommend => __('sn-cms::cms.flags.recommend'),
            self::Top => __('sn-cms::cms.flags.top'),
        };
    }

    /**
     * 预置色名（'danger'）或 Tailwind 色板（Color::Blue）均可，
     * panel 侧由 Filament 原生解析，前端侧由 sn_badge_color() 助手解析
     */
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Hot => 'danger',
            self::New => 'success',
            self::Recommend => Color::Blue,
            self::Top => 'warning',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::Hot => Heroicon::OutlinedFire,
            self::New => Heroicon::OutlinedSparkles,
            self::Recommend => Heroicon::OutlinedStar,
            self::Top => Heroicon::OutlinedArrowUp,
        };
    }
}
