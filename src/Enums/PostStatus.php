<?php

namespace Wsmallnews\Cms\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Wsmallnews\Support\Enums\Traits\EnumHelper;

enum PostStatus: string implements HasColor, HasIcon, HasLabel
{
    use EnumHelper;

    case Draft = 'draft';

    case Published = 'published';

    case Hidden = 'hidden';

    case Scheduled = 'scheduled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => '草稿',
            self::Published => '已发布',
            self::Hidden => '隐藏',
            self::Scheduled => '定时发布',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'info',
            self::Published => 'primary',
            self::Hidden => 'gray',
            self::Scheduled => 'warning',
        };
    }

    public function getIcon(): string | BackedEnum | null
    {
        return match ($this) {
            self::Draft => Heroicon::OutlinedClipboardDocumentList,
            self::Published => Heroicon::OutlinedEye,
            self::Hidden => Heroicon::OutlinedEyeSlash,
            self::Scheduled => Heroicon::OutlinedClock,
        };
    }
}
