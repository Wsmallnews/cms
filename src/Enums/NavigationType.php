<?php

namespace Wsmallnews\Cms\Enums;

use Filament\Support\Contracts\HasLabel;
use Wsmallnews\Support\Enums\Traits\EnumHelper;

enum NavigationType: string implements HasLabel
{

    use EnumHelper;

    case Child = 'child';

    case Route = 'route';

    case Page = 'page';

    case Url = 'url';

    case Content = 'content';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Child => '子导航',
            self::Url => '链接',
            self::Route => '路由',
            self::Page => '页面',
            self::Content => '内容',
        };
    }
}
