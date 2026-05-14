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
            self::Child => __('sn-cms::cms.navigation_type_enum.child'),
            self::Url => __('sn-cms::cms.navigation_type_enum.url'),
            self::Route => __('sn-cms::cms.navigation_type_enum.route'),
            self::Page => __('sn-cms::cms.navigation_type_enum.page'),
            self::Content => __('sn-cms::cms.navigation_type_enum.content'),
        };
    }
}
