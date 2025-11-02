<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Widgets;

use Filament\Widgets\Widget;
use Wsmallnews\Cms\Models\NavigationType;

class NavigationManage extends Widget
{
    protected string $view = 'sn-cms::filament.pages.navigation.widgets.navigation-manage';

    protected int | string | array $columnSpan = 'full';

    public ?NavigationType $record = null;
}
