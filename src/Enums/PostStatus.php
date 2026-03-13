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

    case Pending = 'pending';

    case Published = 'published';

    case Hidden = 'hidden';

    case Scheduled = 'scheduled';

    const Locked = 'locked';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => '草稿',
            self::Pending => '待审核',
            self::Published => '已发布',
            self::Hidden => '隐藏',
            self::Scheduled => '定时发布',
            self::Locked => '已锁定',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'info',
            self::Pending => 'warning',
            self::Published => 'success',
            self::Hidden => 'gray',
            self::Scheduled => 'primary',
            self::Locked => 'danger',
        };
    }

    public function getIcon(): string | BackedEnum | null
    {
        return match ($this) {
            self::Draft => Heroicon::OutlinedClipboardDocumentList,
            self::Pending => Heroicon::OutlinedDocumentCheck,
            self::Published => Heroicon::OutlinedEye,
            self::Hidden => Heroicon::OutlinedEyeSlash,
            self::Scheduled => Heroicon::OutlinedClock,
            self::Locked => Heroicon::OutlinedLockClosed,
        };
    }
}
